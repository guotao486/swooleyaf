<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/6/20 0020
 * Time: 14:05
 */
namespace Map\Tencent;

use Constant\ErrorCode;
use DesignPatterns\Singletons\MapTencentSingleton;
use Exception\Map\TencentMapException;
use Map\SimpleTraitMapBase;

abstract class MapBase {
    use SimpleTraitMapBase;

    const GET_TYPE_SERVER = 'server'; //获取类型-服务端
    const GET_TYPE_MOBILE = 'mobile'; //获取类型-移动端
    const GET_TYPE_BROWSE = 'browse'; //获取类型-网页端

    public function __construct() {
        $this->serverIp = MapTencentSingleton::getInstance()->getConfig()->getServerIp();
        $this->output = 'json';
    }

    /**
     * 服务端IP
     * @var string
     */
    private $serverIp = '';
    /**
     * 页面URL
     * @var string
     */
    private $webUrl = '';
    /**
     * 手机应用标识符
     * @var string
     */
    private $appIdentifier = '';
    /**
     * 返回格式,默认JSON
     * @var string
     */
    private $output = '';

    /**
     * @return string
     */
    public function getServerIp() : string {
        return $this->serverIp;
    }

    /**
     * @return string
     */
    public function getWebUrl() : string {
        return $this->webUrl;
    }

    /**
     * @param string $webUrl
     * @throws \Exception\Map\TencentMapException
     */
    public function setWebUrl(string $webUrl) {
        if(preg_match('/^(http|https)\:\/\/\S+$/', $webUrl) > 0){
            $this->webUrl = $webUrl;
        } else {
            throw new TencentMapException('页面URL不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getAppIdentifier() : string {
        return $this->appIdentifier;
    }

    /**
     * @param string $appIdentifier
     * @throws \Exception\Map\TencentMapException
     */
    public function setAppIdentifier(string $appIdentifier) {
        $identifier = trim($appIdentifier);
        if(strlen($identifier) > 0){
            $this->appIdentifier = $identifier;
        } else {
            throw new TencentMapException('应用标识符不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getOutput() : string {
        return $this->output;
    }

    /**
     * 通过类型获取内容
     * @param string $getType
     * @param array $configs
     * @return void
     * @throws \Exception\Map\TencentMapException
     */
    public function getContentByType(string $getType,array &$configs) {
        switch ($getType) {
            case self::GET_TYPE_BROWSE:
                if(strlen($this->webUrl) == 0){
                    throw new TencentMapException('页面URL不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
                }

                $configs['referer'] = $this->webUrl;
                break;
            case self::GET_TYPE_SERVER:
                if(isset($configs['headers']) && is_array($configs['headers'])){
                    $configs['headers'][] = 'X-FORWARDED-FOR:' . $this->serverIp;
                    $configs['headers'][] = 'CLIENT-IP:' . $this->serverIp;
                } else {
                    $configs['headers'] = [
                        'X-FORWARDED-FOR:' . $this->serverIp,
                        'CLIENT-IP:' . $this->serverIp,
                    ];
                }
                break;
            case self::GET_TYPE_MOBILE:
                if(strlen($this->appIdentifier) == 0){
                    throw new TencentMapException('应用标识符不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
                }

                $configs['referer'] = $this->appIdentifier;
                break;
            default:
                throw new TencentMapException('获取类型不支持', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }
}