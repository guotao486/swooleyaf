<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/6/29 0029
 * Time: 17:16
 */
namespace DesignPatterns\Singletons;

use SySms\AliDaYu\SmsConfig as DaYuConfig;
use SySms\Yun253\SmsConfig as Yun253Config;
use Tool\Tool;
use Traits\SingletonTrait;

class SmsConfigSingleton {
    use SingletonTrait;

    /**
     * 大鱼配置
     * @var \SySms\AliDaYu\SmsConfig
     */
    private $dayuConfig = null;
    /**
     * 253云配置
     * @var \SySms\Yun253\SmsConfig
     */
    private $yun253Config = null;

    private function __construct(){
        $configs = Tool::getConfig('sms.' . SY_ENV . SY_PROJECT);

        //设置大鱼配置
        $dayuConfig = new DaYuConfig();
        $dayuConfig->setAppKey((string)Tool::getArrayVal($configs, 'dayu.app.key', '', true));
        $dayuConfig->setAppSecret((string)Tool::getArrayVal($configs, 'dayu.app.secret', '', true));
        $this->dayuConfig = $dayuConfig;

        $yun253Config = new Yun253Config();
        $yun253Config->setAppKey((string)Tool::getArrayVal($configs, 'yun253.app.key', '', true));
        $yun253Config->setAppSecret((string)Tool::getArrayVal($configs, 'yun253.app.secret', '', true));
        $yun253Config->setAppUrlSend((string)Tool::getArrayVal($configs, 'yun253.app.url.send', '', true));
        $this->yun253Config = $yun253Config;
    }

    /**
     * @return \DesignPatterns\Singletons\SmsConfigSingleton
     */
    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return \SySms\AliDaYu\SmsConfig
     */
    public function getDayuConfig() {
        return $this->dayuConfig;
    }

    /**
     * @return \SySms\Yun253\SmsConfig
     */
    public function getYun253Config() {
        return $this->yun253Config;
    }
}