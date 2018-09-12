<?php
/**
 * 微信配置单例类
 * User: 姜伟
 * Date: 2017/6/17 0017
 * Time: 11:18
 */
namespace DesignPatterns\Singletons;

use Constant\Project;
use Factories\SyBaseMysqlFactory;
use Tool\Tool;
use Traits\SingletonTrait;
use Wx2\WxConfigOpenCommon;
use Wx2\WxConfigShop;

class WxConfigSingleton {
    use SingletonTrait;

    /**
     * 商户平台配置列表
     * @var array
     */
    private $shopConfigs = [];
    /**
     * 开放平台公共配置
     * @var \Wx2\WxConfigOpenCommon
     */
    private $openCommonConfig = null;
    /**
     * 商户平台配置清理时间戳
     * @var int
     */
    private $shopClearTime = 0;

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
        $openCommonConfig->setUrlAuth((string)Tool::getArrayVal($configs, 'open.url.auth', '', true));
        $openCommonConfig->setUrlAuthCallback((string)Tool::getArrayVal($configs, 'open.url.authcallback', '', true));
        $openCommonConfig->setUrlMiniRebindAdmin((string)Tool::getArrayVal($configs, 'open.url.mini.rebindadmin', '', true));
        $openCommonConfig->setDomainMiniServers((array)Tool::getArrayVal($configs, 'open.domain.mini.server', [], true));
        $openCommonConfig->setDomainMiniWebViews((array)Tool::getArrayVal($configs, 'open.domain.mini.webview', [], true));
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
     * @return \Wx2\WxConfigShop|null
     */
    private function getLocalShopConfig(string $appId) {
        $nowTime = Tool::getNowTime();
        if($this->shopClearTime < $nowTime){
            $delIds = [];
            foreach ($this->shopConfigs as $eAppId => $shopConfig) {
                if($shopConfig->getExpireTime() < $nowTime){
                    $delIds[] = $eAppId;
                }
            }
            foreach ($delIds as $eAppId) {
                unset($this->shopConfigs[$eAppId]);
            }

            $this->shopClearTime = $nowTime + Project::TIME_EXPIRE_LOCAL_WXSHOP_CLEAR;
        }

        return Tool::getArrayVal($this->shopConfigs, $appId, null);
    }

    /**
     * 更新商户平台配置
     * @param string $appId
     * @return \Wx2\WxConfigShop
     */
    public function refreshShopConfig(string $appId) {
        $expireTime = Tool::getNowTime() + Project::TIME_EXPIRE_LOCAL_WXSHOP_REFRESH;
        $shopConfig = new WxConfigShop();
        $shopConfig->setAppId($appId);
        $shopConfig->setExpireTime($expireTime);

        $wxConfigEntity = SyBaseMysqlFactory::WxconfigBaseEntity();
        $ormResult1 = $wxConfigEntity->getContainer()->getModel()->getOrmDbTable();
        $ormResult1->where('`app_id`=? AND `status`=?', [$appId, Project::WX_CONFIG_BASE_STATUS_ENABLE]);
        $configInfo = $wxConfigEntity->getContainer()->getModel()->findOne($ormResult1);
        if(empty($configInfo)){
            $shopConfig->setValid(false);
        } else {
            $wxDefaultConfig = Tool::getConfig('project.' . SY_ENV . SY_PROJECT . '.wx');
            $templates = strlen($configInfo['app_templates']) > 0 ? Tool::jsonDecode($configInfo['app_templates']) : [];
            $shopConfig->setValid(true);
            $shopConfig->setClientIp((string)$configInfo['app_clientip']);
            $shopConfig->setSecret((string)$configInfo['app_secret']);
            $shopConfig->setPayMchId((string)$configInfo['pay_mchid']);
            $shopConfig->setPayKey((string)$configInfo['pay_key']);
            $shopConfig->setPayNotifyUrl($wxDefaultConfig['url']['notify']['default']);
            $shopConfig->setPayAuthUrl($wxDefaultConfig['url']['auth']['default']);
            $shopConfig->setSslCert((string)$configInfo['payssl_cert']);
            $shopConfig->setSslKey((string)$configInfo['payssl_key']);
            if (is_array($templates)) {
                $shopConfig->setTemplates($templates);
            }
        }
        unset($configInfo, $ormResult1, $wxConfigEntity);

        $this->shopConfigs[$appId] = $shopConfig;

        return $shopConfig;
    }

    /**
     * 获取商户平台配置
     * @param string $appId
     * @return \Wx2\WxConfigShop|null
     */
    public function getShopConfig(string $appId) {
        $nowTime = Tool::getNowTime();
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
     * @return \Wx2\WxConfigOpenCommon
     */
    public function getOpenCommonConfig(){
        return $this->openCommonConfig;
    }

    /**
     * 设置开放平台公共配置
     * @param \Wx2\WxConfigOpenCommon $config
     */
    public function setOpenCommonConfig(WxConfigOpenCommon $config) {
        $this->openCommonConfig = $config;
    }
}