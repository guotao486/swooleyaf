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
     * 获取access token
     * @param string $appId
     * @return string
     * @throws \Exception\Wx\WxException
     */
    public static function getAccessToken(string $appId) : string {
        $nowTime = Tool::getNowTime();
        $localCacheTag = Project::LOCAL_CACHE_TAG_WX_ACCOUNT . $appId;
        if(SY_CACHE_WXACCOUNT){
            $localCacheData = BaseServer::getWxCache($localCacheTag, '', []);
            if (isset($localCacheData['at_expire']) && ($localCacheData['at_expire'] >= $nowTime)) {
                return $localCacheData['at_content'];
            }
        }

        $redisKey = Project::REDIS_PREFIX_WX_ACCOUNT . $appId;
        $redisData = CacheSimpleFactory::getRedisInstance()->hGetAll($redisKey);
        if (isset($redisData['at_key']) && ($redisData['at_key'] == $redisKey) && ($redisData['at_expire'] >= $nowTime)) {
            if(SY_CACHE_WXACCOUNT){
                $clearTime = $nowTime + Project::TIME_EXPIRE_LOCAL_WXCACHE_CLEAR;
                BaseServer::setWxCache($localCacheTag, [
                    'at_content' => $redisData['at_content'],
                    'at_expire' => (int)$redisData['at_expire'],
                    'clear_time' => $clearTime,
                ]);
            }

            return $redisData['at_content'];
        }

        $accessTokenObj = new AccessToken($appId);
        $accessTokenDetail = $accessTokenObj->getDetail();
        unset($accessTokenObj);

        $expireTime = (int)($nowTime + $accessTokenDetail['expires_in'] - 10);
        CacheSimpleFactory::getRedisInstance()->hMset($redisKey, [
            'at_content' => $accessTokenDetail['access_token'],
            'at_expire' => $expireTime,
            'at_key' => $redisKey,
        ]);
        CacheSimpleFactory::getRedisInstance()->expire($redisKey, 8000);

        if(SY_CACHE_WXACCOUNT){
            $clearTime = $nowTime + Project::TIME_EXPIRE_LOCAL_WXCACHE_CLEAR;
            BaseServer::setWxCache($localCacheTag, [
                'at_content' => $accessTokenDetail['access_token'],
                'at_expire' => $expireTime,
                'clear_time' => $clearTime,
            ]);
        }

        return $accessTokenDetail['access_token'];
    }

    /**
     * 获取js ticket
     * @param string $appId
     * @return string
     * @throws \Exception\Wx\WxException
     */
    public static function getJsTicket(string $appId) : string {
        $nowTime = Tool::getNowTime();
        $localCacheTag = Project::LOCAL_CACHE_TAG_WX_ACCOUNT . $appId;
        if(SY_CACHE_WXACCOUNT){
            $localCacheData = BaseServer::getWxCache($localCacheTag, '', []);
            if (isset($localCacheData['jt_expire']) && ($localCacheData['jt_expire'] >= $nowTime)) {
                return $localCacheData['jt_content'];
            }
        }

        $redisKey = Project::REDIS_PREFIX_WX_ACCOUNT . $appId;
        $redisData = CacheSimpleFactory::getRedisInstance()->hGetAll($redisKey);
        if (isset($redisData['jt_key']) && ($redisData['jt_key'] == $redisKey) && ($redisData['jt_expire'] >= $nowTime)) {
            if(SY_CACHE_WXACCOUNT){
                $clearTime = $nowTime + Project::TIME_EXPIRE_LOCAL_WXCACHE_CLEAR;
                BaseServer::setWxCache($localCacheTag, [
                    'jt_content' => $redisData['jt_content'],
                    'jt_expire' => (int)$redisData['jt_expire'],
                    'clear_time' => $clearTime,
                ]);
            }

            return $redisData['jt_content'];
        }

        $accessToken = self::getAccessToken($appId);
        $jsTicketObj = new JsTicket();
        $jsTicketObj->setAccessToken($accessToken);
        $jsTicketDetail = $jsTicketObj->getDetail();
        unset($jsTicketObj);

        $expireTime = (int)($nowTime + $jsTicketDetail['expires_in'] - 10);
        CacheSimpleFactory::getRedisInstance()->hMset($redisKey, [
            'jt_content' => $jsTicketDetail['ticket'],
            'jt_expire' => $expireTime,
            'jt_key' => $redisKey,
        ]);
        CacheSimpleFactory::getRedisInstance()->expire($redisKey, 8000);

        if(SY_CACHE_WXACCOUNT){
            $clearTime = $nowTime + Project::TIME_EXPIRE_LOCAL_WXCACHE_CLEAR;
            BaseServer::setWxCache($localCacheTag, [
                'jt_content' => $jsTicketDetail['ticket'],
                'jt_expire' => $expireTime,
                'clear_time' => $clearTime,
            ]);
        }

        return $jsTicketDetail['ticket'];
    }
}