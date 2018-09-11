<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/11 0011
 * Time: 11:28
 */
namespace Wx2;

use SyServer\BaseServer;
use Tool\Tool;
use Traits\SimpleTrait;

abstract class WxUtilOpenBase extends WxUtilBase {
    use SimpleTrait;

    /**
     * 获取授权者jsapi ticket
     * @param string $appId 授权者微信号
     * @return string
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