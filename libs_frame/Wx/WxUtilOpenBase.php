<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/11 0011
 * Time: 11:28
 */
namespace Wx;

use Constant\ErrorCode;
use Constant\Project;
use DesignPatterns\Factories\CacheSimpleFactory;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxOpenException;
use SyServer\BaseServer;
use Tool\ProjectTool;
use Tool\Tool;
use Traits\SimpleTrait;
use Wx\OpenCommon\AuthorizerAccessToken;
use Wx\OpenCommon\AuthorizerInfo;
use Wx\OpenCommon\AuthorizerJsTicket;

abstract class WxUtilOpenBase extends WxUtilBase {
    use SimpleTrait;

    /**
     * 获取平台access token
     * @param string $appId 开放平台app id
     * @return string
     * @throws \Exception\Wx\WxOpenException
     */
    public static function getComponentAccessToken(string $appId) : string {
        $redisKey = Project::REDIS_PREFIX_WX_COMPONENT_ACCOUNT . $appId;
        $redisData = CacheSimpleFactory::getRedisInstance()->hGetAll($redisKey);
        if(isset($redisData['unique_key']) && ($redisData['unique_key'] == $redisKey)){
            return $redisData['access_token'];
        }

        throw new WxOpenException('获取平台access token失败', ErrorCode::WXOPEN_PARAM_ERROR);
    }

    /**
     * 获取授权者access token
     * @param string $appId 授权公众号app id
     * @return string
     * @throws \Exception\Wx\WxOpenException
     */
    public static function getAuthorizerAccessToken(string $appId) : string {
        $nowTime = Tool::getNowTime();
        if(SY_CACHE_WXOPEN){
            $localCacheData = BaseServer::getWxOpenAuthorizerTokenCache($appId, '', []);
            if(isset($localCacheData['at_expire']) && ($localCacheData['at_expire'] >= $nowTime)){
                return $localCacheData['at_content'];
            }
        }

        $redisKey = Project::REDIS_PREFIX_WX_COMPONENT_AUTHORIZER . $appId;
        $redisData = CacheSimpleFactory::getRedisInstance()->hGetAll($redisKey);
        if(!isset($redisData['at_key'])){
            $authorizerInfo = ProjectTool::getWxOpenAuthorizerInfo($appId);
            $cacheData['at_key'] = $redisKey;
            $cacheData['auth_code'] = $authorizerInfo['authorizer_authcode'];
            if(strlen($authorizerInfo['authorizer_refreshtoken']) > 0){
                $cacheData['refresh_token'] = $authorizerInfo['authorizer_refreshtoken'];
            } else {
                $openAppId = WxConfigSingleton::getInstance()->getOpenCommonConfig()->getAppId();
                $authorizerInfoObj = new AuthorizerInfo($openAppId);
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
            $refreshToken = $cacheData['refresh_token'];
        } else if($redisData['at_key'] == $redisKey){
            if($redisData['at_expire'] >= $nowTime){
                if(SY_CACHE_WXOPEN){
                    $clearTime = $nowTime + Project::TIME_EXPIRE_LOCAL_WXOPEN_AUTHORIZER_TOKEN_CLEAR;
                    BaseServer::setWxOpenAuthorizerTokenCache($appId, [
                        'at_content' => $redisData['at_content'],
                        'at_expire' => (int)$redisData['at_expire'],
                        'clear_time' => $clearTime,
                    ]);
                }

                return $redisData['at_content'];
            }

            $refreshToken = $redisData['refresh_token'];
        } else {
            throw new WxOpenException('获取缓存失败', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        $authorizerAccessToken = new AuthorizerAccessToken($appId);
        $authorizerAccessToken->setRefreshToken($refreshToken);
        $accessTokenData = $authorizerAccessToken->getDetail();
        unset($authorizerAccessToken);

        $expireTime = (int)($nowTime + $accessTokenData['expires_in'] - 10);
        $cacheData['at_content'] = $accessTokenData['authorizer_access_token'];
        $cacheData['at_expire'] = $expireTime;
        CacheSimpleFactory::getRedisInstance()->hMset($redisKey, $cacheData);
        CacheSimpleFactory::getRedisInstance()->expire($redisKey, 86400);

        if(SY_CACHE_WXOPEN){
            $clearTime = $nowTime + Project::TIME_EXPIRE_LOCAL_WXOPEN_AUTHORIZER_TOKEN_CLEAR;
            BaseServer::setWxOpenAuthorizerTokenCache($appId, [
                'at_content' => $accessTokenData['authorizer_access_token'],
                'at_expire' => $expireTime,
                'clear_time' => $clearTime,
            ]);
        }

        return $accessTokenData['authorizer_access_token'];
    }

    /**
     * 获取授权者js ticket
     * @param string $appId 授权者微信号
     * @return string
     * @throws \Exception\Wx\WxOpenException
     */
    public static function getAuthorizerJsTicket(string $appId) : string {
        $nowTime = Tool::getNowTime();
        if(SY_CACHE_WXOPEN){
            $localCacheData = BaseServer::getWxOpenAuthorizerTokenCache($appId, '', []);
            if(isset($localCacheData['jt_expire']) && ($localCacheData['jt_expire'] >= $nowTime)){
                return $localCacheData['jt_content'];
            }
        }

        $redisKey = Project::REDIS_PREFIX_WX_COMPONENT_AUTHORIZER . $appId;
        $redisData = CacheSimpleFactory::getRedisInstance()->hGetAll($redisKey);
        if (isset($redisData['jt_key']) && ($redisData['jt_key'] == $redisKey) && ($redisData['jt_expire'] >= $nowTime)) {
            if(SY_CACHE_WXOPEN){
                $clearTime = $nowTime + Project::TIME_EXPIRE_LOCAL_WXOPEN_AUTHORIZER_TOKEN_CLEAR;
                BaseServer::setWxOpenAuthorizerTokenCache($appId, [
                    'jt_content' => $redisData['jt_content'],
                    'jt_expire' => (int)$redisData['jt_expire'],
                    'clear_time' => $clearTime,
                ]);
            }

            return $redisData['jt_content'];
        }

        $accessToken = self::getAuthorizerAccessToken($appId);
        $authorizerJsTicket = new AuthorizerJsTicket();
        $authorizerJsTicket->setAccessToken($accessToken);
        $jsTicketData = $authorizerJsTicket->getDetail();
        unset($authorizerJsTicket);

        $expireTime = (int)($nowTime + $jsTicketData['expires_in'] - 10);
        CacheSimpleFactory::getRedisInstance()->hMset($redisKey, [
            'jt_content' => $jsTicketData['ticket'],
            'jt_expire' => $expireTime,
            'jt_key' => $redisKey,
        ]);
        CacheSimpleFactory::getRedisInstance()->expire($redisKey, 86400);

        if(SY_CACHE_WXOPEN){
            $clearTime = $nowTime + Project::TIME_EXPIRE_LOCAL_WXOPEN_AUTHORIZER_TOKEN_CLEAR;
            BaseServer::setWxOpenAuthorizerTokenCache($appId, [
                'jt_content' => $jsTicketData['ticket'],
                'jt_expire' => $expireTime,
                'clear_time' => $clearTime,
            ]);
        }

        return $jsTicketData['ticket'];
    }

    /**
     * 用SHA1算法生成安全签名
     * @param string $token 票据
     * @param string $timestamp 时间戳
     * @param string $nonce 随机字符串
     * @param string $encryptMsg 密文消息
     * @return string
     * @throws \Exception\Wx\WxOpenException
     */
    private static function getSha1Val(string $token,string $timestamp,string $nonce,string $encryptMsg) : string {
        try {
            $saveArr = [$encryptMsg, $token, $timestamp, $nonce];
            sort($saveArr, SORT_STRING);
            $needStr = implode('', $saveArr);

            return sha1($needStr);
        } catch (\Exception $e) {
            throw new WxOpenException('生成安全签名出错', ErrorCode::WXOPEN_PARAM_ERROR);
        }
    }

    /**
     * 填充补位需要加密的明文
     * @param string $text 需要加密的明文
     * @return string
     */
    private static function pkcs7Encode(string $text) : string {
        $blockSize = 32;
        $textLength = strlen($text);
        //计算需要填充的位数
        $addLength = $blockSize - ($textLength % $blockSize);
        if ($addLength == 0) {
            $addLength = $blockSize;
        }

        //获得补位所用的字符
        $needChr = chr($addLength);
        $tmp = '';
        for ($i = 0; $i < $addLength; $i++) {
            $tmp .= $needChr;
        }

        return $text . $tmp;
    }

    /**
     * 补位删除解密后的明文
     * @param string $text 解密后的明文
     * @return string
     */
    private static function pkcs7Decode(string $text) : string {
        $pad = ord(substr($text, -1));
        if (($pad < 1) || ($pad > 32)) {
            $pad = 0;
        }

        return substr($text, 0, (strlen($text) - $pad));
    }

    /**
     * 消息解密
     * @param string $encryptMsg 加密消息
     * @param string $appId 开放平台app id
     * @param string $tag 标识 new：用新的aeskey解密 old：用旧的aeskey解密
     * @return array
     * @throws \Exception\Wx\WxOpenException
     */
    private static function decrypt(string $encryptMsg,string $appId,string $tag='new') : array {
        $openCommonConfig = WxConfigSingleton::getInstance()->getOpenCommonConfig();
        if ($tag == 'new') {
            $aesKey = $openCommonConfig->getAesKeyNow();
            $key = base64_decode($aesKey . '=');
            $iv = substr($key, 0, 16);
        } else {
            $aesKey = $openCommonConfig->getAesKeyBefore();
            $key = base64_decode($aesKey . '=');
            $iv = substr($key, 0, 16);
        }

        $error = '';
        $xml = '';
        $decryptMsg = openssl_decrypt($encryptMsg, 'aes-256-cbc', substr($key, 0, 32), OPENSSL_ZERO_PADDING, $iv);
        $decodeMsg = self::pkcs7Decode($decryptMsg);
        if (strlen($decodeMsg) >= 16) {
            $msgContent = substr($decodeMsg, 16);
            $lengthList = unpack("N", substr($msgContent, 0, 4));
            $xml = substr($msgContent, 4, $lengthList[1]);
            $fromAppId = substr($msgContent, ($lengthList[1] + 4));
            if ($fromAppId != $appId) {
                $error = 'appid不匹配';
            }
        } else {
            $error = '解密失败';
        }

        if (strlen($error) > 0) {
            throw new WxOpenException($error, ErrorCode::WXOPEN_PARAM_ERROR);
        }

        return [
            'aes_key' => $aesKey,
            'content' => $xml,
        ];
    }

    /**
     * 密文解密
     * @param string $encryptXml 密文，对应POST请求的数据
     * @param string $appId 开放平台app id
     * @param string $appToken 开放平台消息校验token
     * @param string $msgSignature 签名串，对应URL参数的msg_signature
     * @param string $nonceStr 随机串，对应URL参数的nonce
     * @param string $timestamp 时间戳 对应URL参数的timestamp
     * @return array
     * @throws \Exception\Wx\WxOpenException
     */
    public static function decryptMsg(string $encryptXml,string $appId,string $appToken,string $msgSignature,string $nonceStr,string $timestamp='') : array {
        if ($timestamp) {
            $nowTime = $timestamp . '';
        } else {
            $nowTime = Tool::getNowTime() . '';
        }

        $signature = self::getSha1Val($appToken, $nowTime, $nonceStr, $encryptXml);
        if ($signature != $msgSignature) {
            throw new WxOpenException('签名验证错误', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        try {
            //用当前的key校验密文
            $res = self::decrypt($encryptXml, $appId, 'new');
        } catch (\Exception $e) {
            //用上次的key校验密文
            $res = self::decrypt($encryptXml, $appId, 'old');
        }

        return $res;
    }

    /**
     * 消息加密
     * @param string $replyMsg 公众平台待回复用户的消息，xml格式的字符串
     * @param string $appId 开放平台app id
     * @param string $aesKey 第三方平台的aes key
     * @param string $nonce 16位随机字符串
     * @return string
     */
    private static function encrypt(string $replyMsg,string $appId,string $aesKey,string $nonce) : string {
        $key = base64_decode($aesKey . '=');
        $iv = substr($key, 0, 16);

        //获得16位随机字符串，填充到明文之前
        $content1 = $nonce . pack("N", strlen($replyMsg)) . $replyMsg . $appId;
        $content2 = self::pkcs7Encode($content1);
        return openssl_encrypt($content2, 'aes-256-cbc', substr($key, 0, 32), OPENSSL_ZERO_PADDING, $iv);
    }

    /**
     * 明文加密
     * @param string $replyMsg 公众平台待回复用户的消息，xml格式的字符串
     * @param string $appId 开放平台app id
     * @param string $appToken 开放平台消息校验token
     * @param string $aesKey 第三方平台的aes key
     * @return string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的xml格式的字符串
     * @throws \Exception\Wx\WxOpenException
     */
    public static function encryptMsg(string $replyMsg,string $appId,string $appToken,string $aesKey) : string {
        $nonceStr = Tool::createNonceStr(16);
        $nowTime = Tool::getNowTime() . '';
        $encryptMsg = self::encrypt($replyMsg, $appId, $aesKey, $nonceStr);
        $signature = self::getSha1Val($appToken, $nowTime, $nonceStr, $encryptMsg);
        $format = "<xml><Encrypt><![CDATA[%s]]></Encrypt><MsgSignature><![CDATA[%s]]></MsgSignature><TimeStamp>%s</TimeStamp><Nonce><![CDATA[%s]]></Nonce></xml>";
        return sprintf($format, $encryptMsg, $signature, $nowTime, $nonceStr);
    }
}