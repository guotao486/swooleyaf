<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/8 0008
 * Time: 16:10
 */
namespace DesignPatterns\Singletons;

use Tool\Tool;
use Traits\SingletonTrait;
use Yun253\ConfigCommon;

class Yun253Singleton {
    use SingletonTrait;

    /**
     * 公共配置
     * @var \Yun253\ConfigCommon
     */
    private $commonConfig = null;

    private function __construct(){
        $configs = Tool::getConfig('yum253.' . SY_ENV . SY_PROJECT);

        $commonConfig = new ConfigCommon();
        $commonConfig->setAppKey((string)Tool::getArrayVal($configs, 'app.key', '', true));
        $commonConfig->setAppSecret((string)Tool::getArrayVal($configs, 'app.secret', '', true));
        $commonConfig->setAppUrlSend((string)Tool::getArrayVal($configs, 'app.url.sms.send', '', true));
        $this->commonConfig = $commonConfig;
    }

    /**
     * @return \DesignPatterns\Singletons\Yun253Singleton
     */
    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return \Yun253\ConfigCommon
     */
    public function getCommonConfig() {
        return $this->commonConfig;
    }
}