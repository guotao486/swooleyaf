<?php
/**
 * 腾讯地图单例类
 * User: 姜伟
 * Date: 2017/6/19 0019
 * Time: 11:13
 */
namespace DesignPatterns\Singletons;

use Constant\ErrorCode;
use Exception\Map\TencentMapException;
use Log\Log;
use Map\Tencent\CoordinateTranslate;
use Map\Tencent\GeoCoder;
use Map\Tencent\GeoCoderReverse;
use Map\Tencent\IpLocation;
use Map\Tencent\MapConfig;
use Map\Tencent\PlaceSearch;
use Map\Tencent\PlaceSuggestion;
use Tool\Tool;
use Traits\SingletonTrait;

class MapTencentSingleton {
    use SingletonTrait;

    private $urlPlaceSearch = 'https://apis.map.qq.com/ws/place/v1/search';
    private $urlPlaceSuggestion = 'https://apis.map.qq.com/ws/place/v1/suggestion';
    private $urlCoordinateTranslate = 'https://apis.map.qq.com/ws/coord/v1/translate';
    private $urlIpLocation = 'https://apis.map.qq.com/ws/location/v1/ip';
    private $urlGeoCoder = 'https://apis.map.qq.com/ws/geocoder/v1/';

    /**
     * @var null|\Map\Tencent\MapConfig
     */
    private $config = null;

    private function __construct() {
        $configs = Tool::getConfig('map.' . SY_ENV . SY_PROJECT);

        $mapConfig = new MapConfig();
        $mapConfig->setKey((string)Tool::getArrayVal($configs, 'tencent.key', '', true));
        $mapConfig->setServerIp((string)Tool::getArrayVal($configs, 'tencent.server.ip', '', true));
        $this->config = $mapConfig;
    }

    /**
     * @return \DesignPatterns\Singletons\MapTencentSingleton
     */
    public static function getInstance() {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return \Map\Tencent\MapConfig|null
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
     * @throws \Exception\Map\TencentMapException
     */
    private function sendPost(string $url,array $data,array $configs=[]) {
        $timeout = (int)Tool::getArrayVal($configs, 'timeout', 1000);
        $referer = Tool::getArrayVal($configs, 'referer', '');
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
        $sendRes = Tool::sendCurlReq($curlConfigs);

        if($sendRes['res_no'] == 0){
            $resData = Tool::jsonDecode($sendRes['res_content']);
            if(is_array($resData)){
                return $resData;
            } else {
                Log::error('解析POST响应失败,响应数据=' . $sendRes['res_content'], ErrorCode::MAP_TENCENT_POST_ERROR);

                throw new TencentMapException('解析POST响应失败', ErrorCode::MAP_TENCENT_POST_ERROR);
            }
        } else {
            Log::error('curl发送腾讯地图post请求出错,错误码=' . $sendRes['res_no'] . ',错误信息=' . $sendRes['res_msg'], ErrorCode::MAP_TENCENT_POST_ERROR);

            throw new TencentMapException('POST请求出错', ErrorCode::MAP_TENCENT_POST_ERROR);
        }
    }

    /**
     * 发送GET请求
     * @param string $url 请求地址
     * @param array $data 数据
     * @param array $configs 配置数组
     * @return array
     * @throws \Exception\Map\TencentMapException
     */
    private function sendGet(string $url,array $data,array $configs=[]) {
        $nowUrl = $url . '?' . http_build_query($data);
        $timeout = (int)Tool::getArrayVal($configs, 'timeout', 1000);
        $referer = Tool::getArrayVal($configs, 'referer', '');
        $headers = Tool::getArrayVal($configs, 'headers', []);

        $curlConfigs = [
            CURLOPT_URL => $nowUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_TIMEOUT_MS => $timeout,
            CURLOPT_HTTPHEADER => $headers,
        ];
        if(strlen($referer) > 0){
            $curlConfigs[CURLOPT_REFERER] = $referer;
        }
        $sendRes = Tool::sendCurlReq($curlConfigs);

        if($sendRes['res_no'] == 0){
            $resData = Tool::jsonDecode($sendRes['res_content']);
            if(is_array($resData)){
                return $resData;
            } else {
                Log::error('解析GET响应失败,响应数据=' . $sendRes['res_content'], ErrorCode::MAP_TENCENT_GET_ERROR);

                throw new TencentMapException('解析GET响应失败', ErrorCode::MAP_TENCENT_GET_ERROR);
            }
        } else {
            Log::error('curl发送腾讯地图get请求出错,错误码=' . $sendRes['res_no'] . ',错误信息=' . $sendRes['res_msg'], ErrorCode::MAP_TENCENT_GET_ERROR);

            throw new TencentMapException('GET请求出错', ErrorCode::MAP_TENCENT_GET_ERROR);
        }
    }

    /**
     * 搜索地区
     * @param \Map\Tencent\PlaceSearch $search 搜索类
     * @param string $searchType 搜索类型 region:地区搜索 nearby:圆形区域搜索 rectangle:矩形区域搜索
     * @param string $getType 获取类型
     * @return array
     * @throws \Exception\Map\TencentMapException
     */
    public function searchPlace(PlaceSearch $search,string $searchType,string $getType) : array {
        $resArr = [
            'code' => 0,
        ];

        if(strlen($search->getKeyword()) == 0){
            throw new TencentMapException('搜索关键字不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }

        $configs = [];
        $search->getContentByType($getType, $configs);

        $data = [
            'keyword' => $search->getKeyword(),
            'boundary' => $search->getAreaSearchContent($searchType),
            'page_size' => $search->getPageSize(),
            'page_index' => $search->getPageIndex(),
            'output' => $search->getOutput(),
            'key' => $this->config->getKey(),
        ];
        if(strlen($search->getFilter()) > 0){
            $data['filter'] = $search->getFilter();
        }
        if(strlen($search->getOrderBy()) > 0){
            $data['orderby'] = $search->getOrderBy();
        }

        $getRes = $this->sendGet($this->urlPlaceSearch, $data, $configs);
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes['data'];
            $resArr['total_num'] = $getRes['count'];
        } else {
            $resArr['code'] = ErrorCode::MAP_TENCENT_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }

    /**
     * 获取地区提示建议
     * @param \Map\Tencent\PlaceSuggestion $suggestion
     * @param string $getType
     * @return array
     * @throws \Exception\Map\TencentMapException
     */
    public function suggestionPlace(PlaceSuggestion $suggestion,string $getType) : array {
        $resArr = [
            'code' => 0,
        ];

        if(strlen($suggestion->getKeyword()) == 0){
            throw new TencentMapException('关键词不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        } else if(strlen($suggestion->getRegion()) == 0){
            throw new TencentMapException('地区不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }

        $configs = [];
        $suggestion->getContentByType($getType, $configs);

        $data = [
            'keyword' => $suggestion->getKeyword(),
            'region' => $suggestion->getRegion(),
            'region_fix' => $suggestion->getRegionLimit(),
            'get_subpois' => $suggestion->getSubLimit(),
            'policy' => $suggestion->getPolicy(),
            'page_index' => $suggestion->getPage(),
            'page_size' => $suggestion->getLimit(),
            'output' => $suggestion->getOutput(),
            'key' => $this->config->getKey(),
        ];
        if(strlen($suggestion->getLocation()) > 0){
            $data['location'] = $suggestion->getLocation();
        }

        $filters = $suggestion->getFilters();
        if(!empty($filters)){
            $filterStr = '';
            foreach ($filters as $key => $val) {
                $filterStr .= ',' . $key . '=' . $val;
            }
            $data['filter'] = substr($filterStr, 1);
        }

        $getRes = $this->sendGet($this->urlPlaceSuggestion, $data, $configs);
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes;
            unset($resArr['data']['status']);
        } else {
            $resArr['code'] = ErrorCode::MAP_TENCENT_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }

    /**
     * 坐标转换
     * @param \Map\Tencent\CoordinateTranslate $coord
     * @param string $getType
     * @return array
     * @throws \Exception\Map\TencentMapException
     */
    public function translateCoord(CoordinateTranslate $coord,string $getType) : array {
        $resArr = [
            'code' => 0,
        ];

        if (empty($coord->getCoords())) {
            throw new TencentMapException('源坐标不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }

        $configs = [];
        $coord->getContentByType($getType, $configs);

        $getRes = $this->sendGet($this->urlCoordinateTranslate, [
            'locations' => implode(';', $coord->getCoords()),
            'type' => $coord->getFromType(),
            'key' => $this->config->getKey(),
            'output' => $coord->getOutput(),
        ], $configs);
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes['locations'];
        } else {
            $resArr['code'] = ErrorCode::MAP_TENCENT_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }

    /**
     * IP定位
     * @param \Map\Tencent\IpLocation $ipLocation
     * @param string $getType
     * @return array
     * @throws \Exception\Map\TencentMapException
     */
    public function getLocationByIp(IpLocation $ipLocation,string $getType) : array {
        $resArr = [
            'code' => 0,
        ];

        if(strlen($ipLocation->getIp()) == 0){
            throw new TencentMapException('ip不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }

        $configs = [];
        $ipLocation->getContentByType($getType, $configs);

        $getRes = $this->sendGet($this->urlIpLocation, [
            'ip' => $ipLocation->getIp(),
            'key' => $this->config->getKey(),
            'output' => $ipLocation->getOutput(),
        ], $configs);
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes['result'];
        } else {
            $resArr['code'] = ErrorCode::MAP_TENCENT_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }

    /**
     * 地址解析
     * @param \Map\Tencent\GeoCoder $geoCoder
     * @param string $getType
     * @return array
     * @throws \Exception\Map\TencentMapException
     */
    public function getGeoCoder(GeoCoder $geoCoder,string $getType) : array {
        $resArr = [
            'code' => 0,
        ];

        if(strlen($geoCoder->getAddress()) == 0){
            throw new TencentMapException('地址不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }

        $configs = [];
        $geoCoder->getContentByType($getType, $configs);

        $data = [
            'address' => $geoCoder->getAddress(),
            'key' => $this->config->getKey(),
            'output' => $geoCoder->getOutput(),
        ];
        if(strlen($geoCoder->getRegion()) > 0){
            $data['region'] = $geoCoder->getRegion();
        }

        $getRes = $this->sendGet($this->urlGeoCoder, $data, $configs);
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes['result'];
        } else {
            $resArr['code'] = ErrorCode::MAP_TENCENT_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }

    /**
     * 逆地址解析
     * @param \Map\Tencent\GeoCoderReverse $coderReverse
     * @param string $getType
     * @return array
     * @throws \Exception\Map\TencentMapException
     */
    public function reverseGeoCoder(GeoCoderReverse $coderReverse,string $getType) : array {
        $resArr = [
            'code' => 0,
        ];

        if(strlen($coderReverse->getLocation()) == 0){
            throw new TencentMapException('坐标不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }

        $configs = [];
        $coderReverse->getContentByType($getType, $configs);

        $data = [
            'location' => $coderReverse->getLocation(),
            'get_poi' => $coderReverse->getPoiStatus(),
            'key' => $this->config->getKey(),
            'output' => $coderReverse->getOutput(),
        ];

        $options = $coderReverse->getPoiOptions();
        if(!empty($options)){
            $optionStr = '';
            foreach ($options as $optKey => $optVal) {
                $optionStr = ';' . $optKey . '=' . $optVal;
            }
            $data['poi_options'] = substr($optionStr, 1);
        }

        $getRes = $this->sendGet($this->urlGeoCoder, $data, $configs);
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes['result'];
        } else {
            $resArr['code'] = ErrorCode::MAP_TENCENT_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }
}