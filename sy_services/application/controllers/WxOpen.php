<?php
class WxOpenController extends CommonController {
    public function init() {
        parent::init();
    }

    /**
     * 处理微信服务器消息通知
     * @api {post} /Index/WxOpen/handleWxNotify 处理微信服务器消息通知
     * @apiDescription 处理微信服务器消息通知
     * @apiGroup ServiceWxOpen
     * @apiParam {string} wx_xml 微信xml消息
     * @apiParam {string} nonce 随机字符串
     * @apiParam {string} msg_signature 消息签名
     * @apiParam {string} encrypt_type 加密方式
     * @apiParam {string} timestamp 时间戳
     * @apiSuccess WxOpenSuccess 请求失败
     * @apiSuccessExample success:
     *     success
     * @apiSuccess WxOpenFail 请求失败
     * @apiSuccessExample fail:
     *     fail
     * @SyFilter-{"field": "wx_xml","explain": "微信xml消息","type": "string","rules": {"required": 1,"min": 1}}
     * @SyFilter-{"field": "nonce","explain": "随机字符串","type": "string","rules": {"required": 1,"regex": "/^[a-zA-Z0-9]{1,32}$/"}}
     * @SyFilter-{"field": "msg_signature","explain": "消息签名","type": "string","rules": {"required": 1,"min": 1}}
     * @SyFilter-{"field": "encrypt_type","explain": "加密方式","type": "string","rules": {"required": 1,"regex": "/^aes$/"}}
     * @SyFilter-{"field": "timestamp","explain": "时间戳","type": "string","rules": {"required": 1,"regex": "/^[1-4]\d{9}$/"}}
     */
    public function handleWxNotifyAction() {
        $allParams = \Request\SyRequest::getParams();
        $incomeData = \Wx\WxOpenUtil::xmlToArray($allParams['wx_xml']);
        if (isset($incomeData['Encrypt']) && isset($incomeData['AppId'])) {
            $nowTime = time();
            $openCommonConfig = \DesignPatterns\Singletons\WxConfigSingleton::getInstance()->getOpenCommonConfig();
            $decryptRes = \Wx\WxOpenUtil::decryptMsg($incomeData['Encrypt'], $openCommonConfig->getAppId(), $allParams['msg_signature'], $allParams['nonce'], $allParams['timestamp']);
            $msgData = \Wx\WxOpenUtil::xmlToArray($decryptRes['content']);
            if($msgData['InfoType'] == 'component_verify_ticket') { //微信服务器定时监听
                \Wx\WxOpenUtil::refreshComponentAccessToken($msgData['ComponentVerifyTicket']);
            } else if($msgData['InfoType'] == 'authorized'){ //授权
                $entity = \Factories\SyBaseMysqlFactory::WxopenAuthorizerEntity();
                $ormResult1 = $entity->getContainer()->getModel()->getOrmDbTable();
                $entity->getContainer()->getModel()->insertOrUpdate($ormResult1, [
                    'component_appid' => $openCommonConfig->getAppId(),
                    'authorizer_appid' => $msgData['AuthorizerAppid'],
                ], [
                    'component_appid' => $openCommonConfig->getAppId(),
                    'authorizer_appid' => $msgData['AuthorizerAppid'],
                    'authorizer_authcode' => $msgData['AuthorizationCode'],
                    'authorizer_status' => \Constant\Server::WX_COMPONENT_AUTHORIZER_STATUS_ALLOW,
                    'created' => $nowTime,
                    'updated' => $nowTime,
                ], [
                    'authorizer_authcode' => $msgData['AuthorizationCode'],
                    'authorizer_refreshtoken' => '',
                    'authorizer_allowpower' => '',
                    'authorizer_info' => '',
                    'authorizer_status' => \Constant\Server::WX_COMPONENT_AUTHORIZER_STATUS_ALLOW,
                    'updated' => $nowTime,
                ]);
                unset($ormResult1, $entity);

                $redisKey = \Constant\Server::REDIS_PREFIX_WX_COMPONENT_AUTHORIZER . $msgData['AuthorizerAppid'];
                \DesignPatterns\Factories\CacheSimpleFactory::getRedisInstance()->del($redisKey);
            } else if($msgData['InfoType'] == 'unauthorized'){ //取消授权
                $entity = \Factories\SyBaseMysqlFactory::WxopenAuthorizerEntity();
                $ormResult1 = $entity->getContainer()->getModel()->getOrmDbTable();
                $entity->getContainer()->getModel()->insertOrUpdate($ormResult1, [
                    'component_appid' => $openCommonConfig->getAppId(),
                    'authorizer_appid' => $msgData['AuthorizerAppid'],
                ], [
                    'component_appid' => $openCommonConfig->getAppId(),
                    'authorizer_appid' => $msgData['AuthorizerAppid'],
                    'authorizer_status' => \Constant\Server::WX_COMPONENT_AUTHORIZER_STATUS_CANCEL,
                    'created' => $nowTime,
                    'updated' => $nowTime,
                ], [
                    'authorizer_refreshtoken' => '',
                    'authorizer_allowpower' => '',
                    'authorizer_info' => '',
                    'authorizer_status' => \Constant\Server::WX_COMPONENT_AUTHORIZER_STATUS_CANCEL,
                    'updated' => $nowTime,
                ]);
                unset($ormResult1, $entity);

                $redisKey = \Constant\Server::REDIS_PREFIX_WX_COMPONENT_AUTHORIZER . $msgData['AuthorizerAppid'];
                \DesignPatterns\Factories\CacheSimpleFactory::getRedisInstance()->del($redisKey);
            } else if($msgData['InfoType'] == 'updateauthorized'){ //更新授权
                $entity = \Factories\SyBaseMysqlFactory::WxopenAuthorizerEntity();
                $ormResult1 = $entity->getContainer()->getModel()->getOrmDbTable();
                $entity->getContainer()->getModel()->insertOrUpdate($ormResult1, [
                    'component_appid' => $openCommonConfig->getAppId(),
                    'authorizer_appid' => $msgData['AuthorizerAppid'],
                ], [
                    'component_appid' => $openCommonConfig->getAppId(),
                    'authorizer_appid' => $msgData['AuthorizerAppid'],
                    'authorizer_authcode' => $msgData['AuthorizationCode'],
                    'authorizer_status' => \Constant\Server::WX_COMPONENT_AUTHORIZER_STATUS_ALLOW,
                    'created' => $nowTime,
                    'updated' => $nowTime,
                ], [
                    'authorizer_authcode' => $msgData['AuthorizationCode'],
                    'authorizer_refreshtoken' => '',
                    'authorizer_allowpower' => '',
                    'authorizer_info' => '',
                    'authorizer_status' => \Constant\Server::WX_COMPONENT_AUTHORIZER_STATUS_ALLOW,
                    'updated' => $nowTime,
                ]);
                unset($ormResult1, $entity);

                $redisKey = \Constant\Server::REDIS_PREFIX_WX_COMPONENT_AUTHORIZER . $msgData['AuthorizerAppid'];
                \DesignPatterns\Factories\CacheSimpleFactory::getRedisInstance()->del($redisKey);
            }

            $this->SyResult->setData('success');
        } else {
            $this->SyResult->setData('fail');
        }

        $this->sendRsp();
    }

    /**
     * 处理授权者公众号消息
     * @api {post} /Index/WxOpen/handleAuthorizerNotify 处理授权者公众号消息
     * @apiDescription 处理授权者公众号消息
     * @apiGroup ServiceWxOpen
     * @apiParam {string} wx_xml 微信xml消息
     * @apiParam {string} appid 授权者公众号id
     * @apiParam {string} openid 用户openid
     * @apiParam {string} nonce 随机字符串
     * @apiParam {string} msg_signature 消息签名
     * @apiParam {string} encrypt_type 加密方式
     * @apiParam {string} timestamp 时间戳
     * @apiSuccess WxOpenSuccess 请求失败
     * @apiSuccessExample success:
     *     <xml><ToUserName>fafasdf</ToUserName><Encrypt>dfdsfaf</Encrypt></xml>
     * @apiSuccess WxOpenFail 请求失败
     * @apiSuccessExample fail:
     *     fail
     * @SyFilter-{"field": "wx_xml","explain": "微信xml消息","type": "string","rules": {"required": 1,"min": 1}}
     * @SyFilter-{"field": "appid","explain": "授权者公众号id","type": "string","rules": {"required": 1,"min": 1}}
     * @SyFilter-{"field": "openid","explain": "用户openid","type": "string","rules": {"required": 1,"regex": "/^[0-9a-zA-Z\-\_]{28}$/"}}
     * @SyFilter-{"field": "nonce","explain": "随机字符串","type": "string","rules": {"required": 1,"regex": "/^[a-zA-Z0-9]{1,32}$/"}}
     * @SyFilter-{"field": "msg_signature","explain": "消息签名","type": "string","rules": {"required": 1,"min": 1}}
     * @SyFilter-{"field": "encrypt_type","explain": "加密方式","type": "string","rules": {"required": 1,"regex": "/^aes$/"}}
     * @SyFilter-{"field": "timestamp","explain": "时间戳","type": "string","rules": {"required": 1,"regex": "/^[1-4]\d{9}$/"}}
     */
    public function handleAuthorizerNotifyAction() {
        $returnStr = 'fail';
        $allParams = \Request\SyRequest::getParams();
        $incomeData = \Wx\WxOpenUtil::xmlToArray($allParams['wx_xml']);
        if (isset($incomeData['Encrypt']) && isset($incomeData['AppId'])) {
            $openCommonConfig = \DesignPatterns\Singletons\WxConfigSingleton::getInstance()->getOpenCommonConfig();
            $decryptRes = \Wx\WxOpenUtil::decryptMsg($incomeData['Encrypt'], $openCommonConfig->getAppId(), $allParams['msg_signature'], $allParams['nonce'], $allParams['timestamp']);
            $msgData = \Wx\WxOpenUtil::xmlToArray($decryptRes['content']);
            if(isset($msgData['MsgType'])){
                $saveArr = [];
                if($msgData['MsgType'] == 'event'){
                    $saveArr = [
                        'ToUserName' => $msgData['FromUserName'],
                        'FromUserName' => $msgData['ToUserName'],
                        'CreateTime' => $msgData['CreateTime'],
                        'MsgType' => 'text',
                        'Content' => $msgData['Event'] . 'from_callback',
                    ];
                } else if($msgData['MsgType'] == 'text'){
                    if($msgData['Content'] == 'TESTCOMPONENT_MSG_TYPE_TEXT'){
                        $saveArr = [
                            'ToUserName' => $msgData['FromUserName'],
                            'FromUserName' => $msgData['ToUserName'],
                            'CreateTime' => $msgData['CreateTime'],
                            'MsgType' => 'text',
                            'Content' => 'TESTCOMPONENT_MSG_TYPE_TEXT_callback',
                        ];
                    } else if(strpos($msgData['Content'], 'QUERY_AUTH_CODE:') === 0){ //全网开通专用
                        $authCode = str_replace('QUERY_AUTH_CODE:', '', $msgData['Content']);
                        //设置返回空消息
                        $saveArr = [
                            'ToUserName' => $msgData['FromUserName'],
                            'FromUserName' => $msgData['ToUserName'],
                            'CreateTime' => $msgData['CreateTime'],
                            'MsgType' => 'text',
                            'Content' => '',
                        ];

                        //使用授权码换取公众号的授权信息
                        $authInfo = \Wx\WxOpenUtil::getAuthorizerAuth($openCommonConfig->getAppId(), $authCode);
                        //调用发送客服消息api回复文本消息
                        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $authInfo['data']['authorization_info']['authorizer_access_token'];
                        \Tool\Tool::sendCurlReq([
                            CURLOPT_URL => $url,
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => \Tool\Tool::jsonEncode([
                                'touser' => $msgData['FromUserName'],
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
                    } else {
                        $saveArr = [
                            'ToUserName' => $msgData['FromUserName'],
                            'FromUserName' => $msgData['ToUserName'],
                            'CreateTime' => $msgData['CreateTime'],
                            'MsgType' => 'text',
                            'Content' => '',
                        ];
                    }
                }
                if(!empty($saveArr)){
                    $replyXml = \Wx\WxOpenUtil::arrayToXml($saveArr);
                    $returnStr = \Wx\WxOpenUtil::encryptMsg($replyXml, $openCommonConfig->getAppId(), $decryptRes['aes_key']);
                }
            }
        }

        $this->SyResult->setData($returnStr);
        $this->sendRsp();
    }
}