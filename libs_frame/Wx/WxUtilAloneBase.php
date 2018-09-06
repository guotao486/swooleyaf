<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/8/31 0031
 * Time: 15:25
 */
namespace Wx;

use Constant\ErrorCode;
use Constant\Project;
use DesignPatterns\Factories\CacheSimpleFactory;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;
use SyServer\BaseServer;
use Tool\Tool;

abstract class WxUtilAloneBase extends WxUtilBase {
    private static $urlAccessToken = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential';
    private static $urlJsTicket = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=';

    /**
     * 刷新access token
     * @param string $appId
     * @return string
     * @throws \Exception\Wx\WxException
     */
    public static function refreshAccessToken(string $appId) : string {
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        if(is_null($shopConfig)){
            throw new WxException('微信appid不支持', ErrorCode::WX_PARAM_ERROR);
        }

        $url = self::$urlAccessToken . '&appid=' . $shopConfig->getAppId() . '&secret=' . $shopConfig->getSecret();
        $data = self::sendGetReq($url);
        $dataArr = Tool::jsonDecode($data);
        if(!is_array($dataArr)){
            throw new WxException('获取access token出错', ErrorCode::WX_PARAM_ERROR);
        } else if(!isset($dataArr['access_token'])){
            throw new WxException($dataArr['errmsg'], ErrorCode::WX_PARAM_ERROR);
        }

        return $dataArr['access_token'];
    }

    /**
     * 刷新jsapi ticket
     * @param string $appId
     * @param string $accessToken
     * @return mixed
     * @throws \Exception\Wx\WxException
     */
    public static function refreshJsTicket(string $appId,string $accessToken) {
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        if(is_null($shopConfig)){
            throw new WxException('微信appid不支持', ErrorCode::WX_PARAM_ERROR);
        }

        $url = self::$urlJsTicket . $accessToken;
        $data = self::sendGetReq($url);
        $dataArr = Tool::jsonDecode($data);
        if(!is_array($dataArr)){
            throw new WxException('获取js ticket出错', ErrorCode::WX_PARAM_ERROR);
        } else if($dataArr['errcode'] > 0){
            throw new WxException($dataArr['errmsg'], ErrorCode::WX_PARAM_ERROR);
        }

        return $dataArr['ticket'];
    }

    /**
     * 刷新微信公众号缓存
     * @param string $appId
     * @return array
     */
    private static function refreshWxAccountCache(string $appId) : array {
        $nowTime = Tool::getNowTime();
        $clearTime = $nowTime + Project::TIME_EXPIRE_LOCAL_WXSHOP_TOKEN_CLEAR;
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

        $accessToken = self::refreshAccessToken($appId);
        $jsTicket = self::refreshJsTicket($appId, $accessToken);
        $expireTime = $nowTime + Project::WX_CONFIG_EXPIRE_TOKEN;
        CacheSimpleFactory::getRedisInstance()->hMset($redisKey, [
            'js_ticket' => $jsTicket,
            'access_token' => $accessToken,
            'expire_time' => $expireTime,
            'unique_key' => $redisKey,
        ]);
        CacheSimpleFactory::getRedisInstance()->expire($redisKey, 7100);

        if(SY_CACHE_WXSHOP){
            BaseServer::setWxShopTokenCache($appId, [
                'access_token' => $accessToken,
                'js_ticket' => $jsTicket,
                'expire_time' => $expireTime,
                'clear_time' => $clearTime,
            ]);
        }

        return [
            'js_ticket' => $jsTicket,
            'access_token' => $accessToken,
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