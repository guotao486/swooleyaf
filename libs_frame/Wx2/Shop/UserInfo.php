<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-12
 * Time: 上午12:22
 */
namespace Wx2\Shop;

use Constant\ErrorCode;
use Exception\Wx\WxException;
use Tool\Tool;
use Wx2\WxBaseShop;
use Wx2\WxUtilBase;
use Wx2\WxUtilBaseAlone;

class UserInfo extends WxBaseShop {
    /**
     * 用户openid
     * @var string
     */
    private $openid = '';

    public function __construct(){
        parent::__construct();
        $this->serviceUrl = 'https://api.weixin.qq.com/cgi-bin/user/info?lang=zh_CN&access_token=';
    }

    public function __clone(){
    }

    /**
     * @param string $openid
     * @throws \Exception\Wx\WxException
     */
    public function setOpenid(string $openid) {
        if (preg_match('/^[0-9a-zA-Z\-\_]{28}$/', $openid) > 0) {
            $this->reqData['openid'] = $openid;
        } else {
            throw new WxException('用户openid不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    public function getDetail(string $appId='') : array {
        if(strlen($appId) == 0){
            throw new WxException('应用ID不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if(!isset($this->reqData['openid'])){
            throw new WxException('用户openid不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr = [
            'code' => 0
        ];

        $this->curlConfigs[CURLOPT_URL] = $this->serviceUrl . WxUtilBaseAlone::getAccessToken($appId) . '&openid=' . $this->reqData['openid'];
        $sendRes = WxUtilBase::sendGetReq($this->curlConfigs);
        $sendData = Tool::jsonDecode($sendRes);
        if (isset($sendData['openid'])) {
            $resArr['data'] = $sendData;
        } else {
            $resArr['code'] = ErrorCode::WX_GET_ERROR;
            $resArr['message'] = $sendData['errmsg'];
        }

        return $resArr;
    }
}