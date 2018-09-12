<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/11 0011
 * Time: 11:32
 */
namespace Wx2\OpenCommon;

use Constant\ErrorCode;
use Exception\Wx\WxOpenException;
use Tool\Tool;
use Wx2\WxBaseOpenCommon;
use Wx2\WxUtilBase;

class AuthorizerJsTicket extends WxBaseOpenCommon {
    /**
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
     * @throws \Exception\Wx\WxOpenException
     */
    public function setAccessToken(string $accessToken) {
        if(strlen($accessToken) > 0){
            $this->accessToken = $accessToken;
        } else {
            throw new WxOpenException('令牌不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(strlen($this->accessToken) == 0){
            throw new WxOpenException('令牌不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $this->curlConfigs[CURLOPT_URL] = $this->serviceUrl . $this->accessToken;
        $sendRes = WxUtilBase::sendGetReq($this->curlConfigs);
        $sendData = Tool::jsonDecode($sendRes);
        if ($sendData['errcode'] != 0) {
            throw new WxOpenException($sendData['errmsg'], ErrorCode::WXOPEN_PARAM_ERROR);
        }

        return $sendData;
    }
}