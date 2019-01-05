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
use Wx\WxConfigOpenCommon;
use Wx\WxConfigAccount;

class WxConfigSingleton {
    use SingletonTrait;

    /**
     * 账号配置列表
     * @var array
     */
    private $accountConfigs = [];
    /**
     * 开放平台公共配置
     * @var \Wx\WxConfigOpenCommon
     */
    private $openCommonConfig = null;
    /**
     * 账号配置清理时间戳
     * @var int
     */
    private $accountClearTime = 0;

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
     * 获取所有的账号配置
     * @return array
     */
    public function getAccountConfigs(){
        return $this->accountConfigs;
    }

    /**
     * 获取本地账号配置
     * @param string $appId
     * @return \Wx\WxConfigAccount|null
     */
    private function getLocalAccountConfig(string $appId) {
        $nowTime = Tool::getNowTime();
        if($this->accountClearTime < $nowTime){
            $delIds = [];
            foreach ($this->accountConfigs as $eAppId => $accountConfig) {
                if($accountConfig->getExpireTime() < $nowTime){
                    $delIds[] = $eAppId;
                }
            }
            foreach ($delIds as $eAppId) {
                unset($this->accountConfigs[$eAppId]);
            }

            $this->accountClearTime = $nowTime + Project::TIME_EXPIRE_LOCAL_WXACCOUNT_CLEAR;
        }

        return Tool::getArrayVal($this->accountConfigs, $appId, null);
    }

    /**
     * 更新账号配置
     * @param string $appId
     * @return \Wx\WxConfigAccount
     */
    private function refreshAccountConfig(string $appId) {
        $expireTime = Tool::getNowTime() + Project::TIME_EXPIRE_LOCAL_WXACCOUNT_REFRESH;
        $accountConfig = new WxConfigAccount();
        $accountConfig->setAppId($appId);
        $accountConfig->setExpireTime($expireTime);

        $wxConfigEntity = SyBaseMysqlFactory::WxconfigBaseEntity();
        $ormResult1 = $wxConfigEntity->getContainer()->getModel()->getOrmDbTable();
        $ormResult1->where('`app_id`=? AND `status`=?', [$appId, Project::WX_CONFIG_STATUS_ENABLE]);
        $configInfo = $wxConfigEntity->getContainer()->getModel()->findOne($ormResult1);
        if(empty($configInfo)){
            $accountConfig->setValid(false);
        } else {
            $wxDefaultConfig = Tool::getConfig('project.' . SY_ENV . SY_PROJECT . '.wx');
            $templates = strlen($configInfo['app_templates']) > 0 ? Tool::jsonDecode($configInfo['app_templates']) : [];
            $accountConfig->setValid(true);
            $accountConfig->setClientIp((string)$configInfo['app_clientip']);
            $accountConfig->setSecret((string)$configInfo['app_secret']);
            $accountConfig->setPayMchId((string)$configInfo['pay_mchid']);
            $accountConfig->setPayKey((string)$configInfo['pay_key']);
            $accountConfig->setPayNotifyUrl($wxDefaultConfig['url']['notify']['default']);
            $accountConfig->setPayAuthUrl($wxDefaultConfig['url']['auth']['default']);
            $accountConfig->setSslCert((string)$configInfo['payssl_cert']);
            $accountConfig->setSslKey((string)$configInfo['payssl_key']);
            if (is_array($templates)) {
                $accountConfig->setTemplates($templates);
            }
        }
        unset($configInfo, $ormResult1, $wxConfigEntity);

        $this->accountConfigs[$appId] = $accountConfig;

        return $accountConfig;
    }

    /**
     * 获取账号配置
     * @param string $appId
     * @return \Wx\WxConfigAccount|null
     */
    public function getShopConfig(string $appId) {
        $nowTime = Tool::getNowTime();
        $accountConfig = $this->getLocalAccountConfig($appId);
        if(is_null($accountConfig)){
            $accountConfig = $this->refreshAccountConfig($appId);
        } else if($accountConfig->getExpireTime() < $nowTime){
            $accountConfig = $this->refreshAccountConfig($appId);
        }

        return $accountConfig->isValid() ? $accountConfig : null;
    }

    /**
     * 移除账号配置
     * @param string $appId
     */
    public function removeAccountConfig(string $appId) {
        unset($this->accountConfigs[$appId]);
    }

    /**
     * 获取开放平台公共配置
     * @return \Wx\WxConfigOpenCommon
     */
    public function getOpenCommonConfig(){
        return $this->openCommonConfig;
    }
}