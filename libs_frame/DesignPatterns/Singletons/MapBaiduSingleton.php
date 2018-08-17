<?php
/**
 * 百度地图单例类
 * User: 姜伟
 * Date: 2017/6/19 0019
 * Time: 11:50
 */
namespace DesignPatterns\Singletons;

use Constant\ErrorCode;
use Exception\Map\BaiduMapException;
use Log\Log;
use Map\BaiDu\CoordinateTranslate;
use Map\BaiDu\GeoCoder;
use Map\BaiDu\GeoCoderReverse;
use Map\BaiDu\IpLocation;
use Map\BaiDu\MapConfig;
use Map\BaiDu\PlaceDetail;
use Map\BaiDu\PlaceSearch;
use Tool\Tool;
use Traits\SingletonTrait;

class MapBaiduSingleton {
    use SingletonTrait;

    private $urlPlaceSearch = 'http://api.map.baidu.com/place/v2/search';
    private $urlPlaceDetail = 'http://api.map.baidu.com/place/v2/detail';
    private $urlCoordinateTranslate = 'http://api.map.baidu.com/geoconv/v1/';
    private $urlIpLocation = 'http://api.map.baidu.com/location/ip';
    private $urlGeoCoder = 'http://api.map.baidu.com/geocoder/v2/';

    /**
     * @var null|\Map\BaiDu\MapConfig
     */
    private $config = null;

    private function __construct() {
        $configs = Tool::getConfig('map.' . SY_ENV . SY_PROJECT);

        $mapConfig = new MapConfig();
        $mapConfig->setAk((string)Tool::getArrayVal($configs, 'baidu.ak', '', true));
        $mapConfig->setServerIp((string)Tool::getArrayVal($configs, 'baidu.server.ip', '', true));
        $this->config = $mapConfig;
    }

    private function __clone(){
    }

    /**
     * @return \DesignPatterns\Singletons\MapBaiduSingleton
     */
    public static function getInstance() {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return \Map\BaiDu\MapConfig|null
     */
    public function getConfig(){
        return $this->config;
    }

    /**
     * 发送POST请求
     * @param string $url 请求地址
     * @param array $data 数据
     * @param array $configs 配置数组
     * @return array
     * @throws \Exception\Map\BaiduMapException
     */
    private function sendPost(string $url,array $data,array $configs=[]) {
        $timeout = (int)Tool::getArrayVal($configs, 'timeout', 1000);
        $referer = Tool::getArrayVal($configs, 'referer', '');
        $userAgent = Tool::getArrayVal($configs, 'user_agent', '');
        $headers = Tool::getArrayVal($configs, 'headers', false);
        if($headers){
            $nowHeaders = $headers;
            $nowHeaders[] = 'Expect:';
        } else {
            $nowHeaders = [
                'Expect:',
            ];
        }

        $curlConfigs = [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT_MS => $timeout,
            CURLOPT_HTTPHEADER => $nowHeaders,
        ];
        if(strlen($referer) > 0){
            $curlConfigs[CURLOPT_REFERER] = $referer;
        }
        if(strlen($userAgent) > 0){
            $curlConfigs[CURLOPT_USERAGENT] = $userAgent;
        }
        $sendRes = Tool::sendCurlReq($curlConfigs);

        if($sendRes['res_no'] == 0){
            $resData = Tool::jsonDecode($sendRes['res_content']);
            if(is_array($resData)){
                return $resData;
            } else {
                Log::error('解析POST响应失败,响应数据=' . $sendRes['res_content'], ErrorCode::MAP_BAIDU_POST_ERROR);

                throw new BaiduMapException('解析POST响应失败', ErrorCode::MAP_BAIDU_POST_ERROR);
            }
        } else {
            Log::error('curl发送百度地图post请求出错,错误码=' . $sendRes['res_no'] . ',错误信息=' . $sendRes['res_msg'], ErrorCode::MAP_BAIDU_POST_ERROR);

            throw new BaiduMapException('POST请求出错', ErrorCode::MAP_BAIDU_POST_ERROR);
        }
    }

    /**
     * 发送GET请求
     * @param string $url 请求地址
     * @param array $data 数据
     * @param array $configs 配置数组
     * @return array
     * @throws \Exception\Map\BaiduMapException
     */
    private function sendGet(string $url,array $data,array $configs=[]) {
        $nowUrl = $url . '?' . http_build_query($data);
        $timeout = (int)Tool::getArrayVal($configs, 'timeout', 1000);
        $referer = Tool::getArrayVal($configs, 'referer', '');
        $userAgent = Tool::getArrayVal($configs, 'user_agent', '');
        $headers = Tool::getArrayVal($configs, 'headers', []);

        $curlConfigs = [
            CURLOPT_URL => $nowUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT_MS => $timeout,
            CURLOPT_HTTPHEADER => $headers,
        ];
        if(strlen($referer) > 0){
            $curlConfigs[CURLOPT_REFERER] = $referer;
        }
        if(strlen($userAgent) > 0){
            $curlConfigs[CURLOPT_USERAGENT] = $userAgent;
        }
        $sendRes = Tool::sendCurlReq($curlConfigs);
        if($sendRes['res_no'] == 0){
            $resData = Tool::jsonDecode($sendRes['res_content']);
            if(is_array($resData)){
                return $resData;
            } else {
                Log::error('解析GET响应失败,响应数据=' . $sendRes['res_content'], ErrorCode::MAP_BAIDU_GET_ERROR);

                throw new BaiduMapException('解析GET响应失败', ErrorCode::MAP_BAIDU_GET_ERROR);
            }
        } else {
            Log::error('curl发送百度地图get请求出错,错误码=' . $sendRes['res_no'] . ',错误信息=' . $sendRes['res_msg'], ErrorCode::MAP_BAIDU_GET_ERROR);

            throw new BaiduMapException('GET请求出错', ErrorCode::MAP_BAIDU_GET_ERROR);
        }
    }

    /**
     * 搜索地区
     * @param \Map\BaiDu\PlaceSearch $search 搜索类
     * @param string $searchType 搜索类型 region:地区搜索 nearby:圆形区域搜索 rectangle:矩形区域搜索
     * @return array
     * @throws \Exception\Map\BaiduMapException
     */
    public function searchPlace(PlaceSearch $search,string $searchType) : array {
        $resArr = [
            'code' => 0,
        ];

        if(empty($search->getKeywords())){
            throw new BaiduMapException('检索关键字不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        $data = [
            'query' => implode('', $search->getKeywords()),
            'output' => $search->getOutput(),
            'scope' => $search->getScope(),
            'coord_type' => $search->getCoordinateType(),
            'page_size' => $search->getPageSize(),
            'page_num' => $search->getPageIndex() - 1,
            'ak' => $this->config->getAk(),
            'timestamp' => Tool::getNowTime(),
        ];
        if(!empty($search->getTags())){
            $data['tag'] = implode(',', $search->getTags());
        }
        if(strlen($search->getFilter()) > 0){
            $data['filter'] = $search->getFilter();
        }
        $trueData = array_merge($data, $search->getAreaSearchContent($searchType));
        $search->setReqData($trueData);
        $search->setReqUrl($this->urlPlaceSearch);
        $search->checkDataByType();

        $getRes = $this->sendGet($this->urlPlaceSearch, $search->getReqData(), $search->getReqConfigs());
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes['results'];
            $resArr['total_num'] = $getRes['total'];
        } else {
            $resArr['code'] = ErrorCode::MAP_BAIDU_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }

    /**
     * 获取poi点的详细信息
     * @param \Map\BaiDu\PlaceDetail $detail
     * @return array
     * @throws \Exception\Map\BaiduMapException
     */
    public function getPoiDetail(PlaceDetail $detail) : array {
        $resArr = [
            'code' => 0,
        ];

        if(empty($detail->getUids())){
            throw new BaiduMapException('uid不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        $detail->setReqData([
            'uids' => implode(',', $detail->getUids()),
            'output' => $detail->getOutput(),
            'scope' => $detail->getScope(),
            'ak' => $this->config->getAk(),
            'timestamp' => Tool::getNowTime(),
        ]);
        $detail->setReqUrl($this->urlPlaceDetail);
        $detail->checkDataByType();

        $getRes = $this->sendGet($this->urlPlaceDetail, $detail->getReqData(), $detail->getReqConfigs());
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes['result'];
        } else {
            $resArr['code'] = ErrorCode::MAP_BAIDU_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }

    /**
     * 坐标转换
     * @param \Map\BaiDu\CoordinateTranslate $coord
     * @return array
     * @throws \Exception\Map\BaiduMapException
     */
    public function translateCoord(CoordinateTranslate $coord) : array {
        $resArr = [
            'code' => 0,
        ];

        if (empty($coord->getCoords())) {
            throw new BaiduMapException('源坐标不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        $coord->setReqData([
            'coords' => implode(';', $coord->getCoords()),
            'ak' => $this->config->getAk(),
            'from' => $coord->getFromType(),
            'to' => $coord->getToType(),
            'output' => $coord->getOutput(),
        ]);
        $coord->setReqUrl($this->urlCoordinateTranslate);
        $coord->checkDataByType();

        $getRes = $this->sendGet($this->urlCoordinateTranslate, $coord->getReqData(), $coord->getReqConfigs());
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes['result'];
        } else {
            $resArr['code'] = ErrorCode::MAP_BAIDU_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }

    /**
     * IP定位
     * @param \Map\BaiDu\IpLocation $ipLocation
     * @return array
     * @throws \Exception\Map\BaiduMapException
     */
    public function getLocationByIp(IpLocation $ipLocation) : array {
        $resArr = [
            'code' => 0,
        ];

        if(strlen($ipLocation->getIp()) == 0){
            throw new BaiduMapException('ip不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        $data = [
            'ip' => $ipLocation->getIp(),
            'ak' => $this->config->getAk(),
        ];
        if(strlen($ipLocation->getReturnCoordType()) > 0){
            $data['coor'] = $ipLocation->getReturnCoordType();
        }

        $ipLocation->setReqData($data);
        $ipLocation->setReqUrl($this->urlIpLocation);
        $ipLocation->checkDataByType();

        $getRes = $this->sendGet($this->urlIpLocation, $ipLocation->getReqData(), $ipLocation->getReqConfigs());
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes;
            unset($resArr['data']['status']);
        } else {
            $resArr['code'] = ErrorCode::MAP_BAIDU_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }

    /**
     * 逆地理编码
     * @param \Map\BaiDu\GeoCoderReverse $geoCoderReverse
     * @return array
     * @throws \Exception\Map\BaiduMapException
     */
    public function reverseGeoCoder(GeoCoderReverse $geoCoderReverse) : array {
        $resArr = [
            'code' => 0,
        ];

        if(strlen($geoCoderReverse->getLocation()) == 0){
            throw new BaiduMapException('坐标地址不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        $data = [
            'location' => $geoCoderReverse->getLocation(),
            'coordtype' => $geoCoderReverse->getCoordType(),
            'ret_coordtype' => $geoCoderReverse->getCoordTypeReturn(),
            'ak' => $this->config->getAk(),
            'output' => $geoCoderReverse->getOutput(),
        ];
        if($geoCoderReverse->getPoiStatus() == 1){
            $data['pois'] = $geoCoderReverse->getPoiStatus();
            $data['radius'] = $geoCoderReverse->getPoiRadius();
        }

        $geoCoderReverse->setReqData($data);
        $geoCoderReverse->setReqUrl($this->urlGeoCoder);
        $geoCoderReverse->checkDataByType();

        $getRes = $this->sendGet($this->urlGeoCoder, $geoCoderReverse->getReqData(), $geoCoderReverse->getReqConfigs());
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes;
            unset($resArr['data']['status']);
        } else {
            $resArr['code'] = ErrorCode::MAP_BAIDU_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }

    /**
     * 地理编码
     * @param \Map\BaiDu\GeoCoder $geoCoder
     * @return array
     * @throws \Exception\Map\BaiduMapException
     */
    public function getGeoCoder(GeoCoder $geoCoder) : array {
        $resArr = [
            'code' => 0,
        ];

        if(strlen($geoCoder->getAddress()) == 0){
            throw new BaiduMapException('地址不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        $geoCoder->setReqData([
            'address' => $geoCoder->getAddress(),
            'city' => $geoCoder->getCityName(),
            'ret_coordtype' => $geoCoder->getCoordTypeReturn(),
            'ak' => $this->config->getAk(),
            'output' => $geoCoder->getOutput(),
        ]);
        $geoCoder->setReqUrl($this->urlGeoCoder);
        $geoCoder->checkDataByType();

        $getRes = $this->sendGet($this->urlGeoCoder, $geoCoder->getReqData(), $geoCoder->getReqConfigs());
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes;
            unset($resArr['data']['status']);
        } else {
            $resArr['code'] = ErrorCode::MAP_BAIDU_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }
}