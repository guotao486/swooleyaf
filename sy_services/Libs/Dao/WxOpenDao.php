<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 18-3-23
 * Time: 上午2:21
 */
namespace Dao;

use Constant\Server;
use DesignPatterns\Factories\CacheSimpleFactory;
use DesignPatterns\Singletons\WxConfigSingleton;
use Factories\SyBaseMysqlFactory;
use Tool\Tool;
use Traits\SimpleDaoTrait;
use Wx\WxUtilOpen;

class WxOpenDao {
    use SimpleDaoTrait;

    private static $notifyWxMap = [
        'component_verify_ticket' => 'handleNotifyWxComponentVerifyTicket',
        'authorized' => 'handleNotifyWxAuthorized',
        'unauthorized' => 'handleNotifyWxUnauthorized',
        'updateauthorized' => 'handleNotifyWxUpdateAuthorized',
    ];
    private static $notifyAuthorizerMap = [
        'event' => 'handleNotifyAuthorizerEvent',
        'text' => 'handleNotifyAuthorizerText',
    ];

    /**
     * 微信服务器定时监听
     * @param array $data
     */
    private static function handleNotifyWxComponentVerifyTicket(array $data) {
        WxUtilOpen::refreshComponentAccessToken($data['ComponentVerifyTicket']);
    }

    /**
     * 授权
     * @param array $data
     */
    private static function handleNotifyWxAuthorized(array $data) {
        $nowTime = time();
        $openCommonConfig = WxConfigSingleton::getInstance()->getOpenCommonConfig();
        $entity = SyBaseMysqlFactory::WxopenAuthorizerEntity();
        $ormResult1 = $entity->getContainer()->getModel()->getOrmDbTable();
        $entity->getContainer()->getModel()->insertOrUpdate($ormResult1, [
            'component_appid' => $openCommonConfig->getAppId(),
            'authorizer_appid' => $data['AuthorizerAppid'],
        ], [
            'component_appid' => $openCommonConfig->getAppId(),
            'authorizer_appid' => $data['AuthorizerAppid'],
            'authorizer_authcode' => $data['AuthorizationCode'],
            'authorizer_status' => Server::WX_COMPONENT_AUTHORIZER_STATUS_ALLOW,
            'created' => $nowTime,
            'updated' => $nowTime,
        ], [
            'authorizer_authcode' => $data['AuthorizationCode'],
            'authorizer_refreshtoken' => '',
            'authorizer_allowpower' => '',
            'authorizer_info' => '',
            'authorizer_status' => Server::WX_COMPONENT_AUTHORIZER_STATUS_ALLOW,
            'updated' => $nowTime,
        ]);
        unset($ormResult1, $entity);

        $redisKey = Server::REDIS_PREFIX_WX_COMPONENT_AUTHORIZER . $data['AuthorizerAppid'];
        CacheSimpleFactory::getRedisInstance()->del($redisKey);
    }

    /**
     * 取消授权
     * @param array $data
     */
    private static function handleNotifyWxUnauthorized(array $data) {
        $nowTime = time();
        $openCommonConfig = WxConfigSingleton::getInstance()->getOpenCommonConfig();
        $entity = SyBaseMysqlFactory::WxopenAuthorizerEntity();
        $ormResult1 = $entity->getContainer()->getModel()->getOrmDbTable();
        $entity->getContainer()->getModel()->insertOrUpdate($ormResult1, [
            'component_appid' => $openCommonConfig->getAppId(),
            'authorizer_appid' => $data['AuthorizerAppid'],
        ], [
            'component_appid' => $openCommonConfig->getAppId(),
            'authorizer_appid' => $data['AuthorizerAppid'],
            'authorizer_status' => Server::WX_COMPONENT_AUTHORIZER_STATUS_CANCEL,
            'created' => $nowTime,
            'updated' => $nowTime,
        ], [
            'authorizer_refreshtoken' => '',
            'authorizer_allowpower' => '',
            'authorizer_info' => '',
            'authorizer_status' => Server::WX_COMPONENT_AUTHORIZER_STATUS_CANCEL,
            'updated' => $nowTime,
        ]);
        unset($ormResult1, $entity);

        $redisKey = Server::REDIS_PREFIX_WX_COMPONENT_AUTHORIZER . $data['AuthorizerAppid'];
        CacheSimpleFactory::getRedisInstance()->del($redisKey);
    }

    /**
     * 更新授权
     * @param array $data
     */
    private static function handleNotifyWxUpdateAuthorized(array $data) {
        $nowTime = time();
        $openCommonConfig = WxConfigSingleton::getInstance()->getOpenCommonConfig();
        $entity = SyBaseMysqlFactory::WxopenAuthorizerEntity();
        $ormResult1 = $entity->getContainer()->getModel()->getOrmDbTable();
        $entity->getContainer()->getModel()->insertOrUpdate($ormResult1, [
            'component_appid' => $openCommonConfig->getAppId(),
            'authorizer_appid' => $data['AuthorizerAppid'],
        ], [
            'component_appid' => $openCommonConfig->getAppId(),
            'authorizer_appid' => $data['AuthorizerAppid'],
            'authorizer_authcode' => $data['AuthorizationCode'],
            'authorizer_status' => Server::WX_COMPONENT_AUTHORIZER_STATUS_ALLOW,
            'created' => $nowTime,
            'updated' => $nowTime,
        ], [
            'authorizer_authcode' => $data['AuthorizationCode'],
            'authorizer_refreshtoken' => '',
            'authorizer_allowpower' => '',
            'authorizer_info' => '',
            'authorizer_status' => Server::WX_COMPONENT_AUTHORIZER_STATUS_ALLOW,
            'updated' => $nowTime,
        ]);
        unset($ormResult1, $entity);

        $redisKey = Server::REDIS_PREFIX_WX_COMPONENT_AUTHORIZER . $data['AuthorizerAppid'];
        CacheSimpleFactory::getRedisInstance()->del($redisKey);
    }

    public static function handleNotifyWx(array $data) {
        $incomeData = WxUtilOpen::xmlToArray($data['wx_xml']);
        if (!isset($incomeData['Encrypt'])) {
            return 'fail';
        }
        if (!isset($incomeData['AppId'])) {
            return 'fail';
        }

        $openCommonConfig = WxConfigSingleton::getInstance()->getOpenCommonConfig();
        $decryptRes = WxUtilOpen::decryptMsg($incomeData['Encrypt'], $openCommonConfig->getAppId(), $openCommonConfig->getToken(), $data['msg_signature'], $data['nonce'], $data['timestamp']);
        $msgData = WxUtilOpen::xmlToArray($decryptRes['content']);
        $funcName = Tool::getArrayVal(self::$notifyWxMap, $msgData['InfoType'], null);
        if (!is_null($funcName)) {
            self::$funcName($msgData);
        }

        return 'success';
    }

    private static function handleNotifyAuthorizerEvent(array $data) : array {
        return [
            'ToUserName' => $data['FromUserName'],
            'FromUserName' => $data['ToUserName'],
            'CreateTime' => $data['CreateTime'],
            'MsgType' => 'text',
            'Content' => $data['Event'] . 'from_callback',
        ];
    }

    private static function handleNotifyAuthorizerText(array $data) : array {
        if($data['Content'] == 'TESTCOMPONENT_MSG_TYPE_TEXT'){
            return [
                'ToUserName' => $data['FromUserName'],
                'FromUserName' => $data['ToUserName'],
                'CreateTime' => $data['CreateTime'],
                'MsgType' => 'text',
                'Content' => 'TESTCOMPONENT_MSG_TYPE_TEXT_callback',
            ];
        } else if(strpos($data['Content'], 'QUERY_AUTH_CODE:') === 0){ //全网开通专用
            $openCommonConfig = WxConfigSingleton::getInstance()->getOpenCommonConfig();
            $authCode = str_replace('QUERY_AUTH_CODE:', '', $data['Content']);
            //使用授权码换取公众号的授权信息
            $authInfo = WxUtilOpen::getAuthorizerAuth($openCommonConfig->getAppId(), $authCode);
            //调用发送客服消息api回复文本消息
            $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $authInfo['data']['authorization_info']['authorizer_access_token'];
            Tool::sendCurlReq([
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => Tool::jsonEncode([
                    'touser' => $data['FromUserName'],
                    'msgtype' => 'text',
                    'text' => [
                        'content' => $authCode . '_from_api',
                    ],
                ], JSON_UNESCAPED_UNICODE),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER => [
                    'Expect:',
                ],
            ]);

            return [
                'ToUserName' => $data['FromUserName'],
                'FromUserName' => $data['ToUserName'],
                'CreateTime' => $data['CreateTime'],
                'MsgType' => 'text',
                'Content' => '',
            ];
        } else {
            return [
                'ToUserName' => $data['FromUserName'],
                'FromUserName' => $data['ToUserName'],
                'CreateTime' => $data['CreateTime'],
                'MsgType' => 'text',
                'Content' => '',
            ];
        }
    }

    public static function handleNotifyAuthorizer(array $data) {
        $incomeData = WxUtilOpen::xmlToArray($data['wx_xml']);
        if(!isset($incomeData['Encrypt'])){
            return 'fail';
        }
        if(!isset($incomeData['AppId'])){
            return 'fail';
        }

        $openCommonConfig = WxConfigSingleton::getInstance()->getOpenCommonConfig();
        $decryptRes = WxUtilOpen::decryptMsg($incomeData['Encrypt'], $openCommonConfig->getAppId(), $openCommonConfig->getToken(), $data['msg_signature'], $data['nonce'], $data['timestamp']);
        $msgData = WxUtilOpen::xmlToArray($decryptRes['content']);
        if(!isset($msgData['MsgType'])){
            return 'fail';
        }

        $funcName = Tool::getArrayVal(self::$notifyAuthorizerMap, $msgData['MsgType'], null);
        if(is_null($funcName)){
            return 'fail';
        }

        $handleRes = self::$funcName($msgData);
        $replyXml = WxUtilOpen::arrayToXml($handleRes);
        return WxUtilOpen::encryptMsg($replyXml, $openCommonConfig->getAppId(), $openCommonConfig->getToken(), $decryptRes['aes_key']);
    }
}