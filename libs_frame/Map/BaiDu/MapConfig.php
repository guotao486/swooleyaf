<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/8/17 0017
 * Time: 15:14
 */
namespace Map\BaiDu;

use Constant\ErrorCode;
use Exception\Map\BaiduMapException;

class MapConfig {
    /**
     * 开发密钥
     * @var string
     */
    private $ak = '';
    /**
     * 服务器IP
     * @var string
     */
    private $serverIp = '';

    public function __construct(){
    }

    private function __clone(){
    }

    /**
     * @return string
     */
    public function getAk() : string {
        return $this->ak;
    }

    /**
     * @param string $ak
     * @throws \Exception\Map\BaiduMapException
     */
    public function setAk(string $ak){
        if(preg_match('/^[0-9a-zA-Z]{32}$/', $ak) > 0){
            $this->ak = $ak;
        } else {
            throw new BaiduMapException('密钥不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

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
    public function setServerIp(string $serverIp){
        if(preg_match('/^(\.(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])){4}$/', '.' . $serverIp) > 0){
            $this->serverIp = $serverIp;
        } else {
            throw new BaiduMapException('服务器IP不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }
}