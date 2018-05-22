<?php
/**
 * 支付宝配置单例类
 * User: 姜伟
 * Date: 2017/6/17 0017
 * Time: 19:15
 */
namespace DesignPatterns\Singletons;

use AliConfigs\DaYu;
use AliConfigs\Pay;
use Constant\Project;
use Constant\Server;
use Factories\SyBaseMysqlFactory;
use Tool\Tool;
use Traits\SingletonTrait;

class AliConfigSingleton {
    use SingletonTrait;

    /**
     * 支付配置列表
     * @var array
     */
    private $payConfigs = [];
    /**
     * 大鱼配置
     * @var \AliConfigs\DaYu
     */
    private $dayuConfig = null;

    /**
     * 支付配置清理时间戳
     * @var int
     */
    private $payClearTime = 0;

    private function __construct() {
        $configs = Tool::getConfig('ali.' . SY_ENV . SY_PROJECT);

        //设置大鱼配置
        $dayuConfig = new DaYu();
        $dayuConfig->setAppKey((string)Tool::getArrayVal($configs, 'dayu.app.key', '', true));
        $dayuConfig->setAppSecret((string)Tool::getArrayVal($configs, 'dayu.app.secret', '', true));
        $this->dayuConfig = $dayuConfig;
    }

    /**
     * @return \DesignPatterns\Singletons\AliConfigSingleton
     */
    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 获取所有的支付配置
     * @return array
     */
    public function getPayConfigs(){
        return $this->payConfigs;
    }

    /**
     * 获取本地支付配置
     * @param string $appId
     * @return \AliConfigs\Pay|null
     */
    private function getLocalPayConfig(string $appId) {
        $nowTime = time();
        if($this->payClearTime < $nowTime){
            $delIds = [];
            foreach ($this->payConfigs as $eAppId => $payConfig) {
                if($payConfig->getExpireTime() < $nowTime){
                    $delIds[] = $eAppId;
                }
            }
            foreach ($delIds as $eAppId) {
                unset($this->payConfigs[$eAppId]);
            }

            $this->payClearTime = $nowTime + Server::TIME_EXPIRE_LOCAL_ALIPAY_CLEAR;
        }

        return Tool::getArrayVal($this->payConfigs, $appId, null);
    }

    /**
     * 更新支付配置
     * @param string $appId
     * @return \AliConfigs\Pay
     */
    public function refreshPayConfig(string $appId) {
        $expireTime = time() + Server::TIME_EXPIRE_LOCAL_ALIPAY_REFRESH;
        $payConfig = new Pay();
        $payConfig->setAppId($appId);
        $payConfig->setExpireTime($expireTime);

        $alipayConfigEntity = SyBaseMysqlFactory::AlipayConfigEntity();
        $ormResult1 = $alipayConfigEntity->getContainer()->getModel()->getOrmDbTable();
        $ormResult1->where('`app_id`=? AND `status`=?', [$appId, Project::ALI_PAY_STATUS_ENABLE]);
        $configInfo = $alipayConfigEntity->getContainer()->getModel()->findOne($ormResult1);
        if(empty($configInfo)){
            $payConfig->setValid(false);
        } else {
            $payConfig->setValid(true);
            $payConfig->setSellerId((string)$configInfo['app_id']);
            $payConfig->setUrlNotify((string)$configInfo['url_notify']);
            $payConfig->setUrlReturn((string)$configInfo['url_return']);
            $payConfig->setPriRsaKey((string)$configInfo['prikey_rsa']);
            $payConfig->setPubRsaKey((string)$configInfo['pubkey_rsa']);
            $payConfig->setPubAliKey((string)$configInfo['pubkey_ali']);
        }
        unset($configInfo, $ormResult1, $alipayConfigEntity);

        $this->payConfigs[$appId] = $payConfig;

        return $payConfig;
    }

    /**
     * 获取支付配置
     * @param string $appId
     * @return \AliConfigs\Pay|null
     */
    public function getPayConfig(string $appId) {
        $nowTime = time();
        $payConfig = $this->getLocalPayConfig($appId);
        if(is_null($payConfig)){
            $payConfig = $this->refreshPayConfig($appId);
        } else if($payConfig->getExpireTime() < $nowTime){
            $payConfig = $this->refreshPayConfig($appId);
        }

        return $payConfig->isValid() ? $payConfig : null;
    }

    /**
     * 移除支付配置
     * @param string $appId
     */
    public function removePayConfig(string $appId) {
        unset($this->payConfigs[$appId]);
    }

    /**
     * 获取大鱼配置
     * @return \AliConfigs\DaYu
     */
    public function getDaYuConfig() {
        return $this->dayuConfig;
    }
}