<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/8/31 0031
 * Time: 15:16
 */
namespace Wx;

use Constant\ErrorCode;
use Tool\Tool;
use Traits\SimpleTrait;
use Wx\Mini\MsgTemplateList;
use Wx\Mini\MsgTemplateSend;

final class WxUtilMini extends WxUtilAloneBase {
    use SimpleTrait;

    private static $urlMsgTemplateList = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token=';
    private static $urlDelMsgTemplate = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/del?access_token=';
    private static $urlSendMsgTemplate = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=';

    /**
     * 获取小程序消息模板列表
     * @param string $appId
     * @param \Wx\Mini\MsgTemplateList $templateList
     * @param string $platType 平台类型 mini：小程序 openmini：第三方平台代理小程序
     * @return array
     */
    public static function getMsgTemplateList(string $appId,MsgTemplateList $templateList,string $platType=self::TYPE_MINI){
        $resArr = [
            'code' => 0
        ];

        if($platType == self::TYPE_MINI){
            $url = self::$urlMsgTemplateList . self::getAccessToken($appId);
        } else {
            $url = self::$urlMsgTemplateList . WxUtilOpenBase::getAuthorizerAccessToken($appId);
        }
        $getRes = self::sendPostReq($url, 'json', $templateList->getDetail(), [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $getData = Tool::jsonDecode($getRes);
        if(isset($getData['list'])){
            $resArr['data'] = $getData;
        } else {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $getData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 删除小程序模板
     * @param string $appId
     * @param string $templateId 模板ID
     * @param string $platType 平台类型 mini：小程序 openmini：第三方平台代理小程序
     * @return array
     */
    public static function delMsgTemplate(string $appId,string $templateId,string $platType=self::TYPE_MINI){
        $resArr = [
            'code' => 0
        ];

        if($platType == self::TYPE_MINI){
            $url = self::$urlDelMsgTemplate . self::getAccessToken($appId);
        } else {
            $url = self::$urlDelMsgTemplate . WxUtilOpenBase::getAuthorizerAccessToken($appId);
        }
        $delRes = self::sendPostReq($url, 'json', [
            'template_id' => $templateId,
        ], [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $delData = Tool::jsonDecode($delRes);
        if($delData['errcode'] == 0){
            $resArr['data'] = $delData;
        } else {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $delData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 发送模板消息
     * @param string $appId
     * @param \Wx\Mini\MsgTemplateSend $templateSend
     * @param string $platType 平台类型 mini：小程序 openmini：第三方平台代理小程序
     * @return array
     */
    public static function sendMsgTemplate(string $appId,MsgTemplateSend $templateSend,string $platType=self::TYPE_MINI){
        $resArr = [
            'code' => 0
        ];

        if($platType == self::TYPE_MINI){
            $url = self::$urlSendMsgTemplate . self::getAccessToken($appId);
        } else {
            $url = self::$urlSendMsgTemplate . WxUtilOpenBase::getAuthorizerAccessToken($appId);
        }
        $sendRes = self::sendPostReq($url, 'json', $templateSend->getDetail(), [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $sendData = Tool::jsonDecode($sendRes);
        if($sendData['errcode'] == 0){
            $resArr['data'] = $sendData;
        } else {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $sendData['errmsg'];
        }

        return $resArr;
    }
}