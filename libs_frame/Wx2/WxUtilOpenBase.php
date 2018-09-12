<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/11 0011
 * Time: 11:28
 */
namespace Wx2;

use Constant\ErrorCode;
use Constant\Project;
use DesignPatterns\Factories\CacheSimpleFactory;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxOpenException;
use SyServer\BaseServer;
use Tool\ProjectTool;
use Tool\Tool;
use Traits\SimpleTrait;

abstract class WxUtilOpenBase extends WxUtilBase {
    use SimpleTrait;

    /**
     * 获取授权者的授权信息
     * @param string $appId 开放平台app id
     * @param string $authCode 授权码
     * @return array
     */
    public static function getAuthorizerAuth(string $appId,string $authCode) : array {
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlAuthorizerAuth . self::getComponentAccessToken($appId);
        $getRes = self::sendPostReq($url, 'json', [
            'component_appid' => $appId,
            'authorization_code' => $authCode,
        ]);
        $getData = Tool::jsonDecode($getRes);
        if (isset($getData['authorization_info'])) {
            $resArr['data'] = $getData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = '授权失败,请重新授权';
        }

        return $resArr;
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
                $authInfo = self::getAuthorizerAuth($openCommonConfig->getAppId(), $authorizerInfo['authorizer_authcode']);
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

        $urlAccessToken = self::$urlAuthorizerToken . self::getComponentAccessToken($openCommonConfig->getAppId());
        $accessTokenRes = self::sendPostReq($urlAccessToken, 'json', [
            'component_appid' => $openCommonConfig->getAppId(),
            'authorizer_appid' => $appId,
            'authorizer_refresh_token' => $refreshToken,
        ]);
        $accessTokenData = Tool::jsonDecode($accessTokenRes);
        if(!isset($accessTokenData['authorizer_access_token'])){
            throw new WxOpenException('获取授权者access token失败', ErrorCode::WXOPEN_POST_ERROR);
        }

        $urlJsTicket = self::$urlJsTicket . $accessTokenData['authorizer_access_token'];
        $jsTicketRes = self::sendGetReq($urlJsTicket);
        $jsTicketData = Tool::jsonDecode($jsTicketRes);
        if ($jsTicketData['errcode'] != 0) {
            throw new WxOpenException($jsTicketData['errmsg'], ErrorCode::WXOPEN_PARAM_ERROR);
        }

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

    /**
     * 获取授权者jsapi ticket
     * @param string $appId 授权者微信号
     * @return string
     * @throws \Exception\Wx\WxOpenException
     */
    public static function getAuthorizerJsTicket(string $appId) : string {
        if(SY_CACHE_WXOPEN){
            $nowTime = Tool::getNowTime();
            $cacheData = BaseServer::getWxOpenAuthorizerTokenCache($appId, '', []);
            if(isset($cacheData['expire_time']) && ($cacheData['expire_time'] >= $nowTime)){
                return $cacheData['js_ticket'];
            }
        }

        $cacheData = self::getAuthorizerCache($appId);
        return $cacheData['js_ticket'];
    }
}