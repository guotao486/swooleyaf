<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/6 0006
 * Time: 14:02
 */
namespace Ali;

use Constant\ErrorCode;
use Exception\Ali\AliPayException;
use Tool\Tool;

class AliConfig {
    /**
     * AppId
     * @var string
     */
    private $appId = '';

    /**
     * 卖家ID
     * @var string
     */
    private $sellerId = '';

    /**
     * 异步消息通知URL
     * @var string
     */
    private $urlNotify = '';

    /**
     * 同步消息通知URL
     * @var string
     */
    private $urlReturn = '';

    /**
     * rsa私钥
     * @var string
     */
    private $priRsaKey = '';

    /**
     * 完整rsa私钥
     * @var string
     */
    private $priRsaKeyFull = '';

    /**
     * rsa公钥
     * @var string
     */
    private $pubRsaKey = '';

    /**
     * 支付宝公钥
     * @var string
     */
    private $pubAliKey = '';

    /**
     * 完整支付宝公钥
     * @var string
     */
    private $pubAliKeyFull = '';

    /**
     * 配置有效状态
     * @var bool
     */
    private $valid = false;

    /**
     * 配置过期时间戳
     * @var int
     */
    private $expireTime = 0;

    public function __construct() {
    }

    private function __clone() {
    }

    /**
     * @return string
     */
    public function getAppId() : string {
        return $this->appId;
    }

    /**
     * @param string $appId
     * @throws \Exception\Ali\AliPayException
     */
    public function setAppId(string $appId) {
        if(preg_match('/^\d{16}$/', $appId) > 0){
            $this->appId = $appId;
        } else {
            throw new AliPayException('app id不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getSellerId(): string {
        return $this->sellerId;
    }

    /**
     * @param string $sellerId
     * @throws \Exception\Ali\AliPayException
     */
    public function setSellerId(string $sellerId) {
        if(preg_match('/^2088\d{12}$/', $sellerId) > 0){
            $this->sellerId = $sellerId;
        } else {
            throw new AliPayException('卖家ID不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getUrlNotify(): string {
        return $this->urlNotify;
    }

    /**
     * @param string $urlNotify
     * @throws \Exception\Ali\AliPayException
     */
    public function setUrlNotify(string $urlNotify) {
        if(preg_match('/^(http|https)\:\/\/\S+$/', $urlNotify) > 0){
            $this->urlNotify = $urlNotify;
        } else {
            throw new AliPayException('异步消息通知URL不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getUrlReturn(): string {
        return $this->urlReturn;
    }

    /**
     * @param string $urlReturn
     * @throws \Exception\Ali\AliPayException
     */
    public function setUrlReturn(string $urlReturn) {
        if(preg_match('/^(http|https)\:\/\/\S+$/', $urlReturn) > 0){
            $this->urlReturn = $urlReturn;
        } else {
            throw new AliPayException('同步消息通知URL不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getPriRsaKey(): string {
        return $this->priRsaKey;
    }

    /**
     * @param string $priRsaKey
     * @throws \Exception\Ali\AliPayException
     */
    public function setPriRsaKey(string $priRsaKey) {
        if(strlen($priRsaKey) >= 1024){
            $this->priRsaKey = $priRsaKey;
            $this->priRsaKeyFull = "-----BEGIN RSA PRIVATE KEY-----" . PHP_EOL . wordwrap($priRsaKey, 64, "\n", true) . PHP_EOL . "-----END RSA PRIVATE KEY-----";
        } else {
            throw new AliPayException('rsa私钥不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getPriRsaKeyFull() : string {
        return $this->priRsaKeyFull;
    }

    /**
     * @return string
     */
    public function getPubRsaKey(): string {
        return $this->pubRsaKey;
    }

    /**
     * @param string $pubRsaKey
     * @throws \Exception\Ali\AliPayException
     */
    public function setPubRsaKey(string $pubRsaKey) {
        if(strlen($pubRsaKey) >= 256){
            $this->pubRsaKey = $pubRsaKey;
        } else {
            throw new AliPayException('rsa公钥不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getPubAliKey(): string {
        return $this->pubAliKey;
    }

    /**
     * @param string $pubAliKey
     * @throws \Exception\Ali\AliPayException
     */
    public function setPubAliKey(string $pubAliKey) {
        if(strlen($pubAliKey) >= 256){
            $this->pubAliKey = $pubAliKey;
            $this->pubAliKeyFull = "-----BEGIN PUBLIC KEY-----" . PHP_EOL . wordwrap($pubAliKey, 64, "\n", true) . PHP_EOL . "-----END PUBLIC KEY-----";
        } else {
            throw new AliPayException('支付宝公钥不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getPubAliKeyFull() : string {
        return $this->pubAliKeyFull;
    }

    /**
     * @return bool
     */
    public function isValid() : bool {
        return $this->valid;
    }

    /**
     * @param bool $valid
     */
    public function setValid(bool $valid) {
        $this->valid = $valid;
    }

    /**
     * @return int
     */
    public function getExpireTime() : int {
        return $this->expireTime;
    }

    /**
     * @param int $expireTime
     */
    public function setExpireTime(int $expireTime) {
        $this->expireTime = $expireTime;
    }

    public function __toString() {
        return Tool::jsonEncode($this->getConfigs(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取配置数组
     * @return array
     */
    public function getConfigs() : array {
        return [
            'appid' => $this->appId,
            'seller.id' => $this->sellerId,
            'url.notify' => $this->urlNotify,
            'url.return' => $this->urlReturn,
            'prikey.rsa' => $this->priRsaKey,
            'pubkey.rsa' => $this->pubRsaKey,
            'pubkey.alipay' => $this->pubAliKey,
            'valid' => $this->valid,
            'expire.time' => $this->expireTime,
        ];
    }
}