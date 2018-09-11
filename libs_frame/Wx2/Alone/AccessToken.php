<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/11 0011
 * Time: 9:48
 */
namespace Wx2\Alone;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;
use Tool\Tool;
use Wx2\WxBaseAlone;
use Wx2\WxUtilBase;

class AccessToken extends WxBaseAlone {
    /**
     * 应用ID
     * @var string
     */
    private $appId = '';

    public function __construct(string $appId){
        parent::__construct();
        $this->appId = $appId;
        $this->serviceUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential';
    }

    public function __clone(){
    }

    public function getDetail() : array {
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($this->appId);
        if(is_null($shopConfig)){
            throw new WxException('微信appid不支持', ErrorCode::WX_PARAM_ERROR);
        }

        $this->curlConfigs[CURLOPT_URL] = $this->serviceUrl . '&appid=' . $shopConfig->getAppId() . '&secret=' . $shopConfig->getSecret();
        $sendRes = WxUtilBase::sendGetReq($this->curlConfigs);
        $sendData = Tool::jsonDecode($sendRes);
        if(!is_array($sendData)){
            throw new WxException('获取access token出错', ErrorCode::WX_PARAM_ERROR);
        } else if(!isset($sendData['access_token'])){
            throw new WxException($sendData['errmsg'], ErrorCode::WX_PARAM_ERROR);
        }

        return $sendData;
    }
}