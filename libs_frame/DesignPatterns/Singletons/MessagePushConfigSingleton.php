<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/6/29 0029
 * Time: 17:16
 */
namespace DesignPatterns\Singletons;

use Constant\ErrorCode;
use Exception\MessagePush\AliPushException;
use SyMessagePush\ConfigAli;
use SyMessagePush\ConfigTencent;
use Tool\Tool;
use Traits\SingletonTrait;

class MessagePushConfigSingleton {
    use SingletonTrait;

    /**
     * 阿里配置
     * @var \SyMessagePush\ConfigAli
     */
    private $aliConfig = null;
    /**
     * 腾讯安卓配置
     * @var \SyMessagePush\ConfigTencent
     */
    private $tencentAndroidConfig = null;
    /**
     * 腾讯苹果配置
     * @var \SyMessagePush\ConfigTencent
     */
    private $tencentIosConfig = null;

    private function __construct(){
        $configs = Tool::getConfig('messagepush.' . SY_ENV . SY_PROJECT);

        //设置阿里配置
        $aliConfig = new ConfigAli();
        $aliConfig->setAccessKey((string)Tool::getArrayVal($configs, 'ali.access.key', '', true));
        $aliConfig->setAccessSecret((string)Tool::getArrayVal($configs, 'ali.access.secret', '', true));
        $aliConfig->setRegionId((string)Tool::getArrayVal($configs, 'ali.region.id', '', true));
        $this->aliConfig = $aliConfig;
        if(!defined('ENABLE_HTTP_PROXY')){
            $proxyStatus = (int)Tool::getArrayVal($configs, 'ali.proxy.status', 0, true);
            if ($proxyStatus > 0) {
                define('ENABLE_HTTP_PROXY', true);
            } else {
                define('ENABLE_HTTP_PROXY', false);
            }
        }
        if(!defined('HTTP_PROXY_IP')){
            $proxyIp = (string)Tool::getArrayVal($configs, 'ali.proxy.ip', '127.0.0.1', true);
            if(preg_match('/^(\.(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])){4}$/', '.' . $proxyIp) > 0){
                define('HTTP_PROXY_IP', $proxyIp);
            } else {
                throw new AliPushException('代理IP不合法', ErrorCode::MESSAGE_PUSH_PARAM_ERROR);
            }
        }
        if(!defined('HTTP_PROXY_PORT')){
            $proxyPort = (int)Tool::getArrayVal($configs, 'ali.proxy.port', 0, true);
            if(($proxyPort > 1000) && ($proxyPort <= 65535)){
                define('HTTP_PROXY_PORT', $proxyPort);
            } else {
                throw new AliPushException('代理端口不合法', ErrorCode::MESSAGE_PUSH_PARAM_ERROR);
            }
        }

        //设置腾讯安卓配置
        $tencentAndroidConfig = new ConfigTencent();
        $tencentAndroidConfig->setPlatform(ConfigTencent::PLATFORM_TYPE_ANDROID);
        $tencentAndroidConfig->setAppId((string)Tool::getArrayVal($configs, 'tencent.android.app.id', '', true));
        $tencentAndroidConfig->setAppSecret((string)Tool::getArrayVal($configs, 'tencent.android.app.secret', '', true));
        $this->tencentAndroidConfig = $tencentAndroidConfig;

        //设置腾讯苹果配置
        $tencentIosConfig = new ConfigTencent();
        $tencentIosConfig->setPlatform(ConfigTencent::PLATFORM_TYPE_IOS);
        $tencentIosConfig->setAppId((string)Tool::getArrayVal($configs, 'tencent.ios.app.id', '', true));
        $tencentIosConfig->setAppSecret((string)Tool::getArrayVal($configs, 'tencent.ios.app.secret', '', true));
        $this->tencentIosConfig = $tencentIosConfig;
    }

    /**
     * @return \DesignPatterns\Singletons\MessagePushConfigSingleton
     */
    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return \SyMessagePush\ConfigAli
     */
    public function getAliConfig() {
        return $this->aliConfig;
    }

    /**
     * @return \SyMessagePush\ConfigTencent
     */
    public function getTencentAndroidConfig() {
        return $this->tencentAndroidConfig;
    }

    /**
     * @return \SyMessagePush\ConfigTencent
     */
    public function getTencentIosConfig() {
        return $this->tencentIosConfig;
    }
}