<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-06-18
 * Time: 15:32
 */
namespace SySms\Yun253;

use Constant\ErrorCode;
use Exception\Sms\Yun253Exception;
use Tool\Tool;

class SmsConfig {
    public function __construct() {
    }

    private function __clone() {
    }

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
    private $appUrlSend = '';

    /**
     * @return string
     */
    public function getAppKey() : string {
        return $this->appKey;
    }

    /**
     * @param string $appKey
     * @throws \Exception\Sms\Yun253Exception
     */
    public function setAppKey(string $appKey) {
        if (ctype_alnum($appKey)) {
            $this->appKey = $appKey;
        } else {
            throw new Yun253Exception('app key不合法', ErrorCode::SMS_PARAM_ERROR);
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
     * @throws \Exception\Sms\Yun253Exception
     */
    public function setAppSecret(string $appSecret) {
        if (ctype_alnum($appSecret)) {
            $this->appSecret = $appSecret;
        } else {
            throw new Yun253Exception('app secret不合法', ErrorCode::SMS_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getAppUrlSend() : string {
        return $this->appUrlSend;
    }

    /**
     * @param string $appUrlSend
     * @throws \Exception\Sms\Yun253Exception
     */
    public function setAppUrlSend(string $appUrlSend){
        if(preg_match('/^(http|https)\:\/\/\S+$/', $appUrlSend) > 0){
            $this->appUrlSend = $appUrlSend;
        } else {
            throw new Yun253Exception('短信下发链接不合法', ErrorCode::SMS_PARAM_ERROR);
        }
    }

    public function __toString() {
        return Tool::jsonEncode([
            'app.key' => $this->appKey,
            'app.secret' => $this->appSecret,
            'app.url.send' => $this->appUrlSend,
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取配置数组
     * @return array
     */
    public function getConfigs() : array {
        return [
            'app.key' => $this->appKey,
            'app.secret' => $this->appSecret,
            'app.url.send' => $this->appUrlSend,
        ];
    }
}