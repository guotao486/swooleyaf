<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-7-15
 * Time: 上午9:36
 */
namespace Wx;

use Constant\ErrorCode;
use Constant\Project;
use Constant\Server;
use DesignPatterns\Factories\CacheSimpleFactory;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxOpenException;
use SyServer\BaseServer;
use Tool\ProjectTool;
use Tool\Tool;
use Wx2\OpenCommon\AuthorizerAccessToken;
use Wx2\OpenCommon\AuthorizerInfo;
use Wx2\OpenCommon\AuthorizerJsTicket;

abstract class WxUtilOpenBase extends WxUtilBase {
    private static $urlJsTicket = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=';

    /**
     * 获取平台access token
     * @param string $appId 开放平台app id
     * @return string
     * @throws \Exception\Wx\WxOpenException
     */
    public static function getComponentAccessToken(string $appId) : string {
        $nowTime = Tool::getNowTime();
        $localKey = Server::CACHE_LOCAL_PREFIX_WXOPEN_ACCESS_TOKEN . $appId;
        $cacheData = BaseServer::getProjectCache($localKey, '', []);
        if(isset($cacheData['expire_time']) && ($cacheData['expire_time'] >= $nowTime)){
            return $cacheData['value'];
        }

        $redisKey = Project::REDIS_PREFIX_WX_COMPONENT_ACCOUNT . $appId;
        $redisData = CacheSimpleFactory::getRedisInstance()->hGetAll($redisKey);
        if(isset($redisData['unique_key']) && ($redisData['unique_key'] == $redisKey)){
            BaseServer::setProjectCache($localKey, [
                'value' => $redisData['access_token'],
                'expire_time' => (int)$redisData['expire_time'],
            ]);

            return $redisData['access_token'];
        }

        throw new WxOpenException('获取平台access token失败', ErrorCode::WXOPEN_PARAM_ERROR);
    }

    /**
     * 获取授权者缓存
     * @param string $appId 授权公众号app id
     * @return string
     * @throws \Exception\Wx\WxOpenException
     */
    private static function getAuthorizerCache(string $appId) : array {
        $nowTime = Tool::getNowTime();
        $clearTime = $nowTime + Project::TIME_EXPIRE_LOCAL_WXOPEN_AUTHORIZER_TOKEN_CLEAR;
        $cacheData = [];
        $openCommonConfig = WxConfigSingleton::getInstance()->getOpenCommonConfig();
        $redisKey = Project::REDIS_PREFIX_WX_COMPONENT_AUTHORIZER . $appId;
        $redisData = CacheSimpleFactory::getRedisInstance()->hGetAll($redisKey);
        if(empty($redisData)){
            $authorizerInfo = ProjectTool::getWxOpenAuthorizerInfo($appId);
            $cacheData['unique_key'] = $redisKey;
            $cacheData['auth_code'] = $authorizerInfo['authorizer_authcode'];
            if(strlen($authorizerInfo['authorizer_refreshtoken']) > 0){
                $cacheData['refresh_token'] = $authorizerInfo['authorizer_refreshtoken'];
            } else {
                $authorizerInfoObj = new AuthorizerInfo($openCommonConfig->getAppId());
                $authorizerInfoObj->setAuthCode($authorizerInfo['authorizer_authcode']);
                $authInfo = $authorizerInfoObj->getDetail();
                unset($authorizerInfoObj);
                if($authInfo['code'] > 0){
                    throw new WxOpenException($authInfo['message'], ErrorCode::WXOPEN_PARAM_ERROR);
                }
                $cacheData['refresh_token'] = $authInfo['data']['authorization_info']['authorizer_refresh_token'];

                ProjectTool::updateWxOpenAuthorizerInfo($appId, [
                    'authorizer_refreshtoken' => $authInfo['data']['authorization_info']['authorizer_refresh_token'],
                    'authorizer_allowpower' => $authInfo['data']['authorization_info']['func_info'],
                    'authorizer_info' => $authInfo['data'],
                ]);
            }
            unset($ormResult1, $entity);
            $refreshToken = $cacheData['refresh_token'];
        } else {
            $uniqueKey = $redisData['unique_key'] ?? '';
            if($uniqueKey != $redisKey){
                throw new WxOpenException('获取缓存失败', ErrorCode::WXOPEN_PARAM_ERROR);
            } else if($redisData['expire_time'] >= $nowTime){
                if(SY_CACHE_WXOPEN){
                    BaseServer::setWxOpenAuthorizerTokenCache($appId, [
                        'access_token' => $redisData['access_token'],
                        'js_ticket' => $redisData['js_ticket'],
                        'expire_time' => (int)$redisData['expire_time'],
                        'clear_time' => $clearTime,
                    ]);
                }

                return [
                    'js_ticket' => $redisData['js_ticket'],
                    'access_token' => $redisData['access_token'],
                ];
            }

            $refreshToken = $redisData['refresh_token'];
        }

        $authorizerAccessToken = new AuthorizerAccessToken($appId);
        $authorizerAccessToken->setRefreshToken($refreshToken);
        $accessTokenData = $authorizerAccessToken->getDetail();
        unset($authorizerAccessToken);

        $authorizerJsTicket = new AuthorizerJsTicket();
        $authorizerJsTicket->setAccessToken($accessTokenData['authorizer_access_token']);
        $jsTicketData = $authorizerJsTicket->getDetail();
        unset($authorizerJsTicket);

        $expireTime = $nowTime + Project::WX_COMPONENT_AUTHORIZER_EXPIRE_TOKEN;
        $cacheData['js_ticket'] = $jsTicketData['ticket'];
        $cacheData['access_token'] = $accessTokenData['authorizer_access_token'];
        $cacheData['expire_time'] = $expireTime;
        CacheSimpleFactory::getRedisInstance()->hMset($redisKey, $cacheData);
        CacheSimpleFactory::getRedisInstance()->expire($redisKey, 86400);

        if(SY_CACHE_WXOPEN){
            BaseServer::setWxOpenAuthorizerTokenCache($appId, [
                'access_token' => $accessTokenData['authorizer_access_token'],
                'js_ticket' => $jsTicketData['ticket'],
                'expire_time' => $expireTime,
                'clear_time' => $clearTime,
            ]);
        }

        return [
            'js_ticket' => $jsTicketData['ticket'],
            'access_token' => $accessTokenData['authorizer_access_token'],
        ];
    }

    /**
     * 获取授权者access token
     * @param string $appId 授权公众号app id
     * @return string
     * @throws \Exception\Wx\WxOpenException
     */
    public static function getAuthorizerAccessToken(string $appId) : string {
        if(SY_CACHE_WXOPEN){
            $nowTime = Tool::getNowTime();
            $cacheData = BaseServer::getWxOpenAuthorizerTokenCache($appId, '', []);
            if(isset($cacheData['expire_time']) && ($cacheData['expire_time'] >= $nowTime)){
                return $cacheData['access_token'];
            }
        }

        $cacheData = self::getAuthorizerCache($appId);
        return $cacheData['access_token'];
    }
}