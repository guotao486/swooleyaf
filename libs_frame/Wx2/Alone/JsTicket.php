<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/11 0011
 * Time: 10:39
 */
namespace Wx2\Alone;

use Constant\ErrorCode;
use Exception\Wx\WxException;
use Tool\Tool;
use Wx2\WxBaseAlone;
use Wx2\WxUtilBase;

class JsTicket extends WxBaseAlone {
    /**
     * 令牌
     * @var string
     */
    private $accessToken = '';

    public function __construct(){
        parent::__construct();
        $this->serviceUrl = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=';
    }

    public function __clone(){
    }

    /**
     * @param string $accessToken
     * @throws \Exception\Wx\WxException
     */
    public function setAccessToken(string $accessToken){
        if(strlen($accessToken) > 0){
            $this->accessToken = $accessToken;
        } else {
            throw new WxException('令牌不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(strlen($this->accessToken) == 0){
            throw new WxException('令牌不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $this->curlConfigs[CURLOPT_URL] = $this->serviceUrl . $this->accessToken;
        $sendRes = WxUtilBase::sendGetReq($this->curlConfigs);
        $sendData = Tool::jsonDecode($sendRes);
        if(!is_array($sendData)){
            throw new WxException('获取js ticket出错', ErrorCode::WX_PARAM_ERROR);
        } else if($sendData['errcode'] > 0){
            throw new WxException($sendData['errmsg'], ErrorCode::WX_PARAM_ERROR);
        }

        return $sendData;
    }
}