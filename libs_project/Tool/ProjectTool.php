<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/5/22 0022
 * Time: 10:12
 */
namespace Tool;

use Constant\ErrorCode;
use Constant\Project;
use DesignPatterns\Factories\CacheSimpleFactory;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxOpenException;
use Factories\SyBaseMysqlFactory;
use Traits\SimpleTrait;

final class ProjectTool {
    use SimpleTrait;

    /**
     * 生成唯一单号
     * @return string
     */
    public static function createUniqueSn() : string {
        $redis = CacheSimpleFactory::getRedisInstance();
        $needStr = date('YmdHis');
        $uniqueSn = $needStr . mt_rand(10000000, 99999999);
        $redisKey = Project::REDIS_PREFIX_ORDER_SN . $uniqueSn;
        while($redis->exists($redisKey)) {
            $uniqueSn = $needStr . mt_rand(10000000, 99999999);
            $redisKey = Project::REDIS_PREFIX_ORDER_SN . $uniqueSn;
        }
        $redis->set($redisKey, '1', 10);

        return $uniqueSn;
    }

    /**
     * 处理微信开放平台公众号授权
     * @param int $optionType 操作类型
     * @param array $data
     * @throws \Exception\Wx\WxOpenException
     */
    public static function handleAppAuthForWxOpen(int $optionType,array $data) {
        $nowTime = time();
        $openCommonConfig = WxConfigSingleton::getInstance()->getOpenCommonConfig();
        $entity = SyBaseMysqlFactory::WxopenAuthorizerEntity();
        $ormResult1 = $entity->getContainer()->getModel()->getOrmDbTable();

        switch ($optionType) {
            case Project::WX_COMPONENT_AUTHORIZER_OPTION_TYPE_AUTHORIZED:
                $entity->getContainer()->getModel()->insertOrUpdate($ormResult1, [
                    'component_appid' => $openCommonConfig->getAppId(),
                    'authorizer_appid' => $data['AuthorizerAppid'],
                ], [
                    'component_appid' => $openCommonConfig->getAppId(),
                    'authorizer_appid' => $data['AuthorizerAppid'],
                    'authorizer_authcode' => $data['AuthorizationCode'],
                    'authorizer_status' => Project::WX_COMPONENT_AUTHORIZER_STATUS_ALLOW,
                    'created' => $nowTime,
                    'updated' => $nowTime,
                ], [
                    'authorizer_authcode' => $data['AuthorizationCode'],
                    'authorizer_refreshtoken' => '',
                    'authorizer_allowpower' => '',
                    'authorizer_info' => '',
                    'authorizer_status' => Project::WX_COMPONENT_AUTHORIZER_STATUS_ALLOW,
                    'updated' => $nowTime,
                ]);
                break;
            case Project::WX_COMPONENT_AUTHORIZER_OPTION_TYPE_UNAUTHORIZED:
                $entity->getContainer()->getModel()->insertOrUpdate($ormResult1, [
                    'component_appid' => $openCommonConfig->getAppId(),
                    'authorizer_appid' => $data['AuthorizerAppid'],
                ], [
                    'component_appid' => $openCommonConfig->getAppId(),
                    'authorizer_appid' => $data['AuthorizerAppid'],
                    'authorizer_authcode' => $data['AuthorizationCode'],
                    'authorizer_status' => Project::WX_COMPONENT_AUTHORIZER_STATUS_ALLOW,
                    'created' => $nowTime,
                    'updated' => $nowTime,
                ], [
                    'authorizer_authcode' => $data['AuthorizationCode'],
                    'authorizer_refreshtoken' => '',
                    'authorizer_allowpower' => '',
                    'authorizer_info' => '',
                    'authorizer_status' => Project::WX_COMPONENT_AUTHORIZER_STATUS_ALLOW,
                    'updated' => $nowTime,
                ]);
                break;
            case Project::WX_COMPONENT_AUTHORIZER_OPTION_TYPE_AUTHORIZED_UPDATE:
                $entity->getContainer()->getModel()->insertOrUpdate($ormResult1, [
                    'component_appid' => $openCommonConfig->getAppId(),
                    'authorizer_appid' => $data['AuthorizerAppid'],
                ], [
                    'component_appid' => $openCommonConfig->getAppId(),
                    'authorizer_appid' => $data['AuthorizerAppid'],
                    'authorizer_status' => Project::WX_COMPONENT_AUTHORIZER_STATUS_CANCEL,
                    'created' => $nowTime,
                    'updated' => $nowTime,
                ], [
                    'authorizer_refreshtoken' => '',
                    'authorizer_allowpower' => '',
                    'authorizer_info' => '',
                    'authorizer_status' => Project::WX_COMPONENT_AUTHORIZER_STATUS_CANCEL,
                    'updated' => $nowTime,
                ]);
                break;
            default:
                throw new WxOpenException('授权操作类型不支持', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        unset($ormResult1, $entity);
        $redisKey = Project::REDIS_PREFIX_WX_COMPONENT_AUTHORIZER . $data['AuthorizerAppid'];
        CacheSimpleFactory::getRedisInstance()->del($redisKey);
    }

    /**
     * 获取微信开放平台授权公众号信息
     * @param string $appId 授权公众号app id
     * @return array
     * @throws \Exception\Wx\WxOpenException
     */
    public static function getWxOpenAuthorizerInfo(string $appId){
        $entity = SyBaseMysqlFactory::WxopenAuthorizerEntity();
        $ormResult1 = $entity->getContainer()->getModel()->getOrmDbTable();
        $ormResult1->where('`component_appid`=? AND `authorizer_appid`=?', [WxConfigSingleton::getInstance()->getOpenCommonConfig()->getAppId(), $appId,]);
        $authorizerInfo = $entity->getContainer()->getModel()->findOne($ormResult1);
        if(empty($authorizerInfo)){
            throw new WxOpenException('授权公众号不存在', ErrorCode::WXOPEN_PARAM_ERROR);
        } else if($authorizerInfo['authorizer_status'] != Project::WX_COMPONENT_AUTHORIZER_STATUS_ALLOW){
            throw new WxOpenException('授权公众号已取消授权', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        return $authorizerInfo;
    }

    /**
     * 更新微信开放平台授权公众号信息
     * @param string $appId 授权公众号app id
     * @param array $data
     */
    public static function updateWxOpenAuthorizerInfo(string $appId,array $data){
        $entity = SyBaseMysqlFactory::WxopenAuthorizerEntity();
        $ormResult1 = $entity->getContainer()->getModel()->getOrmDbTable();
        $ormResult1->where('`component_appid`=? AND `authorizer_appid`=?', [WxConfigSingleton::getInstance()->getOpenCommonConfig()->getAppId(), $appId,]);
        $entity->getContainer()->getModel()->update($ormResult1, [
            'authorizer_refreshtoken' => $data['authorizer_refreshtoken'],
            'authorizer_allowpower' => Tool::jsonEncode($data['authorizer_allowpower'], JSON_UNESCAPED_UNICODE),
            'authorizer_info' => Tool::jsonEncode($data['authorizer_info'], JSON_UNESCAPED_UNICODE),
            'updated' => time(),
        ]);
    }
}