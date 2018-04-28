<?php
/**
 * 微信配置单例类
 * User: 姜伟
 * Date: 2017/6/17 0017
 * Time: 11:18
 */
namespace DesignPatterns\Singletons;

use Constant\Server;
use Factories\SyBaseMysqlFactory;
use Tool\Tool;
use Traits\SingletonTrait;
use Wx\WxConfigOpenCommon;
use Wx\WxConfigShop;

class WxConfigSingleton {
    use SingletonTrait;

    /**
     * 商户平台配置列表
     * @var array
     */
    private $shopConfigs = [];
    /**
     * 开放平台公共配置
     * @var WxConfigOpenCommon
     */
    private $openCommonConfig = null;

    /**
     * @return \DesignPatterns\Singletons\WxConfigSingleton
     */
    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct(){
        $configs = Tool::getConfig('wx.' . SY_ENV . SY_PROJECT);

        //初始化开放平台公共配置
        $openCommonConfig = new WxConfigOpenCommon();
        $openCommonConfig->setExpireComponentAccessToken((int)Tool::getArrayVal($configs,'open.expire.component.accesstoken', 0, true));
        $openCommonConfig->setExpireAuthorizerJsTicket((int)Tool::getArrayVal($configs,'open.expire.authorizer.jsticket', 0, true));
        $openCommonConfig->setExpireAuthorizerAccessToken((int)Tool::getArrayVal($configs,'open.expire.authorizer.accesstoken', 0, true));
        $openCommonConfig->setAppId((string)Tool::getArrayVal($configs, 'open.appid', '', true));
        $openCommonConfig->setSecret((string)Tool::getArrayVal($configs, 'open.secret', '', true));
        $openCommonConfig->setToken((string)Tool::getArrayVal($configs, 'open.token', '', true));
        $openCommonConfig->setAesKeyBefore((string)Tool::getArrayVal($configs, 'open.aeskey.before', '', true));
        $openCommonConfig->setAesKeyNow((string)Tool::getArrayVal($configs, 'open.aeskey.now', '', true));
        $openCommonConfig->setAuthUrlDomain((string)Tool::getArrayVal($configs, 'open.authurl.domain', '', true));
        $openCommonConfig->setAuthUrlCallback((string)Tool::getArrayVal($configs, 'open.authurl.callback', '', true));
        $this->openCommonConfig = $openCommonConfig;
    }

    private function __clone(){
    }

    /**
     * 获取所有的商户平台配置
     * @return array
     */
    public function getShopConfigs(){
        return $this->shopConfigs;
    }

    /**
     * 获取本地商户平台配置
     * @param string $appId
     * @return \Wx\WxConfigShop|null
     */
    private function getLocalShopConfig(string $appId) {
        return Tool::getArrayVal($this->shopConfigs, $appId, null);
    }

    /**
     * 更新商户平台配置
     * @param string $appId
     * @return \Wx\WxConfigShop
     */
    public function refreshShopConfig(string $appId) {
        $expireTime = time() + Server::TIME_EXPIRE_LOCAL_WXSHOP_CACHE;
        $shopConfig = new WxConfigShop();
        $shopConfig->setAppId($appId);
        $shopConfig->setExpireTime($expireTime);

        $wxshopConfigEntity = SyBaseMysqlFactory::WxshopConfigEntity();
        $ormResult1 = $wxshopConfigEntity->getContainer()->getModel()->getOrmDbTable();
        $ormResult1->where('`app_id`=? AND `status`=?', [$appId, Server::WX_SHOP_STATUS_ENABLE]);
        $configInfo = $wxshopConfigEntity->getContainer()->getModel()->findOne($ormResult1);
        if(empty($configInfo)){
            $shopConfig->setValid(false);
        } else {
            $templates = strlen($configInfo['app_templates']) > 0 ? Tool::jsonDecode($configInfo['app_templates']) : [];
            $shopConfig->setValid(true);
            $shopConfig->setExpireJsTicket(7000);
            $shopConfig->setExpireAccessToken(7000);
            $shopConfig->setClientIp((string)$configInfo['app_clientip']);
            $shopConfig->setSecret((string)$configInfo['app_secret']);
            $shopConfig->setPayMchId((string)$configInfo['pay_mchid']);
            $shopConfig->setPayKey((string)$configInfo['pay_key']);
            $shopConfig->setPayNotifyUrl((string)$configInfo['payurl_notify']);
            $shopConfig->setPayAuthUrl((string)$configInfo['payurl_auth']);
            $shopConfig->setSslCert((string)$configInfo['payssl_cert']);
            $shopConfig->setSslKey((string)$configInfo['payssl_key']);
            if (is_array($templates)) {
                $shopConfig->setTemplates($templates);
            }
        }
        unset($configInfo, $ormResult1, $wxshopConfigEntity);

        $this->shopConfigs[$appId] = $shopConfig;

        return $shopConfig;
    }

    /**
     * 获取商户平台配置
     * @param string $appId
     * @return \Wx\WxConfigShop|null
     */
    public function getShopConfig(string $appId) {
        $nowTime = time();
        $shopConfig = $this->getLocalShopConfig($appId);
        if(is_null($shopConfig)){
            $shopConfig = $this->refreshShopConfig($appId);
        } else if($shopConfig->getExpireTime() < $nowTime){
            $shopConfig = $this->refreshShopConfig($appId);
        }

        return $shopConfig->isValid() ? $shopConfig : null;
    }

    /**
     * 移除商户平台配置
     * @param string $appId
     */
    public function removeShopConfig(string $appId) {
        unset($this->shopConfigs[$appId]);
    }

    /**
     * 获取商户模板ID
     * @param string $appId
     * @param string $name 模板名称
     * @return string|null
     */
    public function getShopTemplateId(string $appId,string $name) {
        $shopConfig = $this->getShopConfig($appId);
        if (is_null($shopConfig)) {
            return null;
        }

        return Tool::getArrayVal($shopConfig->getTemplates(), $name, null);
    }

    /**
     * 获取开放平台公共配置
     * @return \Wx\WxConfigOpenCommon
     */
    public function getOpenCommonConfig(){
        return $this->openCommonConfig;
    }

    /**
     * 设置开放平台公共配置
     * @param \Wx\WxConfigOpenCommon $config
     */
    public function setOpenCommonConfig(WxConfigOpenCommon $config) {
        $this->openCommonConfig = $config;
    }
}