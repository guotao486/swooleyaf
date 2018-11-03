<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/1 0001
 * Time: 10:26
 */
namespace Ali\Auth;

use Ali\AliBase;
use Constant\ErrorCode;
use Exception\Ali\AliAuthException;

class OauthToken extends AliBase {
    /**
     * 准许类型
     * @var string
     */
    private $grant_type = '';
    /**
     * 授权码
     * @var string
     */
    private $code = '';
    /**
     * 刷新令牌
     * @var string
     */
    private $refresh_token = '';

    public function __construct(string $appId){
        parent::__construct($appId);
        $this->setMethod('alipay.system.oauth.token');
    }

    private function __clone(){
    }

    /**
     * @param string $grantType
     * @throws \Exception\Ali\AliAuthException
     */
    public function setGrantType(string $grantType){
        if(in_array($grantType, ['authorization_code', 'refresh_token',])){
            $this->biz_content['grant_type'] = $grantType;
        } else {
            throw new AliAuthException('准许类型不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $code
     * @throws \Exception\Ali\AliAuthException
     */
    public function setCode(string $code){
        if(ctype_alnum($code) && (strlen($code) <= 128)){
            $this->biz_content['code'] = $code;
            unset($this->biz_content['refresh_token']);
        } else {
            throw new AliAuthException('授权码不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $refreshToken
     * @throws \Exception\Ali\AliAuthException
     */
    public function setRefreshToken(string $refreshToken){
        if(ctype_alnum($refreshToken) && (strlen($refreshToken) <= 40)){
            $this->biz_content['refresh_token'] = $refreshToken;
            unset($this->biz_content['code']);
        } else {
            throw new AliAuthException('刷新令牌不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if (!isset($this->biz_content['grant_type'])) {
            throw new AliAuthException('准许类型不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if(($this->biz_content['grant_type'] == 'authorization_code') && !isset($this->biz_content['code'])){
            throw new AliAuthException('授权码不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        } else if(($this->biz_content['grant_type'] == 'refresh_token') && !isset($this->biz_content['refresh_token'])){
            throw new AliAuthException('刷新令牌不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        return $this->getContent();
    }
}