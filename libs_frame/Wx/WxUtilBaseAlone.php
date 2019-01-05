<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/11 0011
 * Time: 9:00
 */
namespace Wx;

use Constant\Project;
use DesignPatterns\Factories\CacheSimpleFactory;
use SyServer\BaseServer;
use Tool\Tool;
use Traits\SimpleTrait;
use Wx\Alone\AccessToken;
use Wx\Alone\JsTicket;

abstract class WxUtilBaseAlone extends WxUtilBase {
    use SimpleTrait;

    /**
     * 刷新微信公众号缓存
     * @param string $appId
     * @return array
     */
    private static function refreshWxAccountCache(string $appId) : array {
        $nowTime = Tool::getNowTime();
        $clearTime = $nowTime + Project::TIME_EXPIRE_LOCAL_WXACCOUNT_TOKEN_CLEAR;
        $redisKey = Project::REDIS_PREFIX_WX_ACCOUNT . $appId;
        $redisData = CacheSimpleFactory::getRedisInstance()->hGetAll($redisKey);
        if (isset($redisData['unique_key']) && ($redisData['unique_key'] == $redisKey) && ($redisData['expire_time'] >= $nowTime)) {
            if(SY_CACHE_WXSHOP){
                $expireTime = (int)$redisData['expire_time'];
                BaseServer::setWxShopTokenCache($appId, [
                    'access_token' => $redisData['access_token'],
                    'js_ticket' => $redisData['js_ticket'],
                    'expire_time' => $expireTime,
                    'clear_time' => $clearTime,
                ]);
            }

            return [
                'js_ticket' => $redisData['js_ticket'],
                'access_token' => $redisData['access_token'],
            ];
        }

        $accessTokenObj = new AccessToken($appId);
        $accessTokenDetail = $accessTokenObj->getDetail();
        unset($accessTokenObj);

        $jsTicketObj = new JsTicket();
        $jsTicketObj->setAccessToken($accessTokenDetail['access_token']);
        $jsTicketDetail = $jsTicketObj->getDetail();
        unset($jsTicketObj);

        $expireTime = $nowTime + Project::WX_CONFIG_EXPIRE_TOKEN;
        CacheSimpleFactory::getRedisInstance()->hMset($redisKey, [
            'js_ticket' => $jsTicketDetail['ticket'],
            'access_token' => $accessTokenDetail['access_token'],
            'expire_time' => $expireTime,
            'unique_key' => $redisKey,
        ]);
        CacheSimpleFactory::getRedisInstance()->expire($redisKey, 7100);

        if(SY_CACHE_WXSHOP){
            BaseServer::setWxShopTokenCache($appId, [
                'access_token' => $accessTokenDetail['access_token'],
                'js_ticket' => $jsTicketDetail['ticket'],
                'expire_time' => $expireTime,
                'clear_time' => $clearTime,
            ]);
        }

        return [
            'js_ticket' => $jsTicketDetail['ticket'],
            'access_token' => $accessTokenDetail['access_token'],
        ];
    }

    /**
     * 获取access token
     * @param string $appId
     * @return string
     */
    public static function getAccessToken(string $appId) : string {
        if(SY_CACHE_WXSHOP){
            $nowTime = Tool::getNowTime();
            $localCacheData = BaseServer::getWxShopTokenCache($appId, '', []);
            if (isset($localCacheData['expire_time']) && ($localCacheData['expire_time'] >= $nowTime)) {
                return $localCacheData['access_token'];
            }
        }

        $cacheData = self::refreshWxAccountCache($appId);
        return $cacheData['access_token'];
    }

    /**
     * 获取jsapi ticket
     * @param string $appId
     * @return string
     */
    public static function getJsTicket(string $appId) : string {
        if(SY_CACHE_WXSHOP){
            $nowTime = Tool::getNowTime();
            $localCacheData = BaseServer::getWxShopTokenCache($appId, '', []);
            if (isset($localCacheData['expire_time']) && ($localCacheData['expire_time'] >= $nowTime)) {
                return $localCacheData['js_ticket'];
            }
        }

        $cacheData = self::refreshWxAccountCache($appId);
        return $cacheData['js_ticket'];
    }
}