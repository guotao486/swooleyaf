<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/8 0008
 * Time: 15:51
 */
namespace Yun253;

use Constant\ErrorCode;
use Exception\Yun253\SmsException;
use Tool\Tool;

class ConfigCommon {
    /**
     * APP KEY
     * @var string
     */
    private $appKey = '';
    /**
     * APP 密钥
     * @var string
     */
    private $appSecret = '';
    /**
     * APP短信下发链接
     * @var string
     */
    private $urlSmsSend = '';

    public function __construct() {
    }

    private function __clone() {
    }

    /**
     * @return string
     */
    public function getAppKey() : string {
        return $this->appKey;
    }

    /**
     * @param string $appKey
     * @throws \Exception\Yun253\SmsException
     */
    public function setAppKey(string $appKey) {
        if (ctype_alnum($appKey)) {
            $this->appKey = $appKey;
        } else {
            throw new SmsException('app key不合法', ErrorCode::SMS_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getAppSecret() : string {
        return $this->appSecret;
    }

    /**
     * @param string $appSecret
     * @throws \Exception\Yun253\SmsException
     */
    public function setAppSecret(string $appSecret) {
        if (ctype_alnum($appSecret)) {
            $this->appSecret = $appSecret;
        } else {
            throw new SmsException('app secret不合法', ErrorCode::SMS_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getUrlSmsSend() : string {
        return $this->urlSmsSend;
    }

    /**
     * @param string $urlSmsSend
     * @throws \Exception\Yun253\SmsException
     */
    public function setUrlSmsSend(string $urlSmsSend){
        if(preg_match('/^(http|https)\:\/\/\S+$/', $urlSmsSend) > 0){
            $this->urlSmsSend = $urlSmsSend;
        } else {
            throw new SmsException('短信下发链接不合法', ErrorCode::SMS_PARAM_ERROR);
        }
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
            'app.key' => $this->appKey,
            'app.secret' => $this->appSecret,
            'url.sms.send' => $this->urlSmsSend,
        ];
    }
}