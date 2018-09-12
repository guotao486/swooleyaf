<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-12
 * Time: 下午11:53
 */
namespace Wx2\OpenMini;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Tool\Tool;
use Wx2\WxBaseOpenMini;
use Wx2\WxUtilBase;
use Wx2\WxUtilOpenBase;

class TemplateCodeList extends WxBaseOpenMini {
    public function __construct(){
        parent::__construct();
        $this->serviceUrl = 'https://api.weixin.qq.com/wxa/gettemplatelist?access_token=';
    }

    public function __clone(){
    }

    public function getDetail() : array {
        $resArr = [
            'code' => 0,
        ];

        $this->curlConfigs[CURLOPT_URL] = $this->serviceUrl . WxUtilOpenBase::getComponentAccessToken(WxConfigSingleton::getInstance()->getOpenCommonConfig()->getAppId());
        $sendRes = WxUtilBase::sendGetReq($this->curlConfigs);
        $sendData = Tool::jsonDecode($sendRes);
        if($sendData['errcode'] == 0){
            $resArr['data'] = $sendData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_GET_ERROR;
            $resArr['message'] = $sendData['errmsg'];
        }

        return $resArr;
    }
}