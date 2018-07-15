<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 18-3-23
 * Time: 上午2:21
 */
namespace Dao;

use Constant\Project;
use DesignPatterns\Singletons\WxConfigSingleton;
use Tool\ProjectTool;
use Tool\Tool;
use Traits\SimpleDaoTrait;
use Wx\WxUtilOpenBase;

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
        WxUtilOpenBase::refreshComponentAccessToken($data['ComponentVerifyTicket']);
    }

    /**
     * 授权
     * @param array $data
     */
    private static function handleNotifyWxAuthorized(array $data) {
        ProjectTool::handleAppAuthForWxOpen(Project::WX_COMPONENT_AUTHORIZER_OPTION_TYPE_AUTHORIZED, $data);
    }

    /**
     * 取消授权
     * @param array $data
     */
    private static function handleNotifyWxUnauthorized(array $data) {
        ProjectTool::handleAppAuthForWxOpen(Project::WX_COMPONENT_AUTHORIZER_OPTION_TYPE_UNAUTHORIZED, $data);
    }

    /**
     * 更新授权
     * @param array $data
     */
    private static function handleNotifyWxUpdateAuthorized(array $data) {
        ProjectTool::handleAppAuthForWxOpen(Project::WX_COMPONENT_AUTHORIZER_OPTION_TYPE_AUTHORIZED_UPDATE, $data);
    }

    public static function handleNotifyWx(array $data) {
        $incomeData = WxUtilOpenBase::xmlToArray($data['wx_xml']);
        if (!isset($incomeData['Encrypt'])) {
            return 'fail';
        }
        if (!isset($incomeData['AppId'])) {
            return 'fail';
        }

        $openCommonConfig = WxConfigSingleton::getInstance()->getOpenCommonConfig();
        $decryptRes = WxUtilOpenBase::decryptMsg($incomeData['Encrypt'], $openCommonConfig->getAppId(), $openCommonConfig->getToken(), $data['msg_signature'], $data['nonce'], $data['timestamp']);
        $msgData = WxUtilOpenBase::xmlToArray($decryptRes['content']);
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
            $authInfo = WxUtilOpenBase::getAuthorizerAuth($openCommonConfig->getAppId(), $authCode);
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
        $incomeData = WxUtilOpenBase::xmlToArray($data['wx_xml']);
        if(!isset($incomeData['Encrypt'])){
            return 'fail';
        }
        if(!isset($incomeData['AppId'])){
            return 'fail';
        }

        $openCommonConfig = WxConfigSingleton::getInstance()->getOpenCommonConfig();
        $decryptRes = WxUtilOpenBase::decryptMsg($incomeData['Encrypt'], $openCommonConfig->getAppId(), $openCommonConfig->getToken(), $data['msg_signature'], $data['nonce'], $data['timestamp']);
        $msgData = WxUtilOpenBase::xmlToArray($decryptRes['content']);
        if(!isset($msgData['MsgType'])){
            return 'fail';
        }

        $funcName = Tool::getArrayVal(self::$notifyAuthorizerMap, $msgData['MsgType'], null);
        if(is_null($funcName)){
            return 'fail';
        }

        $handleRes = self::$funcName($msgData);
        $replyXml = WxUtilOpenBase::arrayToXml($handleRes);
        return WxUtilOpenBase::encryptMsg($replyXml, $openCommonConfig->getAppId(), $openCommonConfig->getToken(), $decryptRes['aes_key']);
    }
}