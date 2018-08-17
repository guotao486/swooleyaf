<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/6/20 0020
 * Time: 14:05
 */
namespace Map\BaiDu;

use Constant\ErrorCode;
use Exception\Map\BaiduMapException;

abstract class BaseConfig {
    const CHECK_TYPE_SERVER_IP = 'server-ip'; //校验类型-服务端ip
    const CHECK_TYPE_SERVER_SN = 'server-sn'; //校验类型-服务端签名
    const CHECK_TYPE_BROWSE = 'browse'; //校验类型-浏览器

    public function __construct() {
        $this->output = 'json';
        $this->checkType = self::CHECK_TYPE_SERVER_IP;
        $this->reqMethod = 'GET';
    }

    private function __clone() {
    }

    public function getConfigs() : array {
        return get_object_vars($this);
    }

    /**
     * 服务端IP
     * @var string
     */
    private $serverIp = '';
    /**
     * 用户签名
     * @var string
     */
    private $sk = '';
    /**
     * 输出格式
     * @var string
     */
    private $output = '';
    /**
     * 校验类型
     * @var string
     */
    private $checkType = '';
    /**
     * 请求引用地址
     * @var string
     */
    private $reqReferer = '';
    /**
     * 请求数据
     * @var array
     */
    private $reqData = [];
    /**
     * 请求配置
     * @var array
     */
    private $reqConfigs = [];
    /**
     * 请求地址
     * @var string
     */
    private $reqUrl = '';
    /**
     * 请求方式
     * @var string
     */
    private $reqMethod = '';

    /**
     * @return string
     */
    public function getServerIp() : string {
        return $this->serverIp;
    }

    /**
     * @param string $serverIp
     * @throws \Exception\Map\BaiduMapException
     */
    public function setServerIp(string $serverIp) {
        if(preg_match('/^(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])(\.(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])){3}$/', $serverIp) > 0){
            $this->serverIp = $serverIp;
        } else {
            throw new BaiduMapException('服务端IP不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getSk() : string {
        return $this->sk;
    }

    /**
     * @param string $sk
     * @throws \Exception\Map\BaiduMapException
     */
    public function setSk(string $sk) {
        if(preg_match('/^[0-9a-zA-Z]{32}$/', $sk) > 0){
            $this->sk = $sk;
        } else {
            throw new BaiduMapException('用户签名不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getOutput() : string {
        return $this->output;
    }

    /**
     * @return string
     */
    public function getCheckType() : string {
        return $this->checkType;
    }

    /**
     * @param string $checkType
     * @throws \Exception\Map\BaiduMapException
     */
    public function setCheckType(string $checkType) {
        if(in_array($checkType, [self::CHECK_TYPE_SERVER_IP, self::CHECK_TYPE_SERVER_SN, self::CHECK_TYPE_BROWSE], true)){
            $this->checkType = $checkType;
        } else {
            throw new BaiduMapException('校验类型不支持', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getReqReferer() : string {
        return $this->reqReferer;
    }

    /**
     * @param string $reqReferer
     * @throws \Exception\Map\BaiduMapException
     */
    public function setReqReferer(string $reqReferer) {
        if(preg_match('/^(http|https)\:\/\/\S+$/', $reqReferer) > 0){
            $this->reqReferer = $reqReferer;
        } else {
            throw new BaiduMapException('请求引用地址不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return array
     */
    public function getReqData() : array {
        return $this->reqData;
    }

    /**
     * @param array $reqData
     */
    public function setReqData(array $reqData) {
        $this->reqData = $reqData;
    }

    /**
     * @return array
     */
    public function getReqConfigs() : array {
        return $this->reqConfigs;
    }

    /**
     * @param array $reqConfigs
     */
    public function setReqConfigs(array $reqConfigs) {
        $this->reqConfigs = $reqConfigs;
    }

    /**
     * @param string $reqUrl
     * @throws \Exception\Map\BaiduMapException
     */
    public function setReqUrl(string $reqUrl) {
        if(preg_match('/^(http|https)\:\/\/\S+$/', $reqUrl) > 0){
            $this->reqUrl = $reqUrl;
        } else {
            throw new BaiduMapException('请求地址不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @param string $reqMethod
     * @throws \Exception\Map\BaiduMapException
     */
    public function setReqMethod(string $reqMethod) {
        if(in_array($reqMethod, ['GET', 'POST'], true)){
            $this->reqMethod = $reqMethod;
        } else {
            throw new BaiduMapException('请求方式不支持', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * 根据校验类型检查请求数据
     * @throws \Exception\Map\BaiduMapException
     */
    public function checkDataByType() {
        switch ($this->checkType) {
            case self::CHECK_TYPE_SERVER_IP:
                if(strlen($this->serverIp) == 0){
                    throw new BaiduMapException('服务端IP不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
                }

                if(isset($this->reqConfigs['headers']) && is_array($this->reqConfigs['headers'])){
                    $this->reqConfigs['headers'][] = 'X-FORWARDED-FOR:' . $this->serverIp;
                    $this->reqConfigs['headers'][] = 'CLIENT-IP:' . $this->serverIp;
                } else {
                    $this->reqConfigs['headers'] = [
                        'X-FORWARDED-FOR:' . $this->serverIp,
                        'CLIENT-IP:' . $this->serverIp,
                    ];
                }
                break;
            case self::CHECK_TYPE_SERVER_SN:
                if(strlen($this->sk) == 0){
                    throw new BaiduMapException('签名校验码不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
                }
                if(strlen($this->reqUrl) == 0){
                    throw new BaiduMapException('请求地址不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
                }
                if(empty($this->reqData)){
                    throw new BaiduMapException('请求数据不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
                }

                if ($this->reqMethod === 'POST'){
                    ksort($this->reqData);
                }
                $str = $this->reqUrl . '?' . http_build_query($this->reqData) . $this->sk;
                $this->reqData['sn'] = md5(urlencode($str));
                break;
            case self::CHECK_TYPE_BROWSE:
                if(strlen($this->reqReferer) == 0){
                    throw new BaiduMapException('请求引用地址不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
                }

                $this->reqConfigs['referer'] = $this->reqReferer;
                $this->reqConfigs['user_agent'] = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11';
                break;
            default:
                break;
        }
    }
}