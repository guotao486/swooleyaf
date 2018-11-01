<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/1 0001
 * Time: 10:08
 */
namespace Ali\Auth;

use Ali\AliBase;
use Constant\ErrorCode;
use Exception\Ali\AliPayException;

class AuthTokenQuery extends AliBase {
    /**
     * 应用授权令牌
     * @var string
     */
    private $app_auth_token = '';

    public function __construct(string $appId){
        parent::__construct($appId);
        $this->setMethod('alipay.open.auth.token.app.query');
    }

    private function __clone(){
    }

    /**
     * @param string $appAuthToken
     * @throws \Exception\Ali\AliPayException
     */
    public function setAppAuthToken(string $appAuthToken){
        if(ctype_alnum($appAuthToken) && (strlen($appAuthToken) <= 128)){
            $this->biz_content['app_auth_token'] = $appAuthToken;
        } else {
            throw new AliPayException('应用授权令牌不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if (!isset($this->biz_content['app_auth_token'])) {
            throw new AliPayException('应用授权令牌不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        return $this->getContent();
    }
}