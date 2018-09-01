<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/8/31 0031
 * Time: 15:16
 */
namespace Wx;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Tool\Tool;
use Traits\SimpleTrait;
use Wx\Mini\MsgTemplate;
use Wx\Mini\MsgTemplateList;
use Wx\Mini\MsgTemplateTitleList;
use Wx\Mini\Qrcode;

class WxUtilMini extends WxUtilAloneBase {
    use SimpleTrait;

    private static $urlAuthorize = 'https://api.weixin.qq.com/sns/jscode2session?grant_type=authorization_code&appid=';
    private static $urlQrcode = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=';
    private static $urlMsgTemplateTitleList = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/library/list?access_token=';
    private static $urlMsgTemplateTitleKeywords = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/library/get?access_token=';
    private static $urlAddMsgTemplate = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/add?access_token=';
    private static $urlMsgTemplateList = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token=';
    private static $urlDelMsgTemplate = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/del?access_token=';

    /**
     * 处理用户小程序授权
     * @param string $code 换取授权access_token的票据
     * @param string $appId
     * @return array
     */
    public static function handleUserAuthorize(string $code,string $appId) : array {
        $resArr = [
            'code' => 0
        ];

        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $url = self::$urlAuthorize . $shopConfig->getAppId() . '&secret=' . $shopConfig->getSecret() . '&js_code=' . $code;
        $getRes = self::sendGetReq($url);
        $getData = Tool::jsonDecode($getRes);
        if(isset($getData['openid'])){
            $resArr['data'] = $getData;
        } else {
            $resArr['code'] = ErrorCode::WX_GET_ERROR;
            $resArr['message'] = $getData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 获取小程序二维码
     * @param string $appId
     * @param \Wx\Mini\Qrcode $qrcode
     * @return array
     */
    public static function getQrcode(string $appId,Qrcode $qrcode){
        $resArr = [
            'code' => 0
        ];

        $url = self::$urlQrcode . self::getAccessToken($appId);
        $getRes = self::sendPostReq($url, 'json', $qrcode->getDetail());
        $getData = Tool::jsonDecode($getRes);
        if(is_array($getData)){
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $getData['errmsg'];
        } else {
            $resArr['data'] = [
                'image' => base64_encode($getRes),
            ];
        }

        return $resArr;
    }

    /**
     * 解密小程序数据
     * @param string $encryptedData 加密数据
     * @param string $iv 初始向量
     * @param string $sessionKey 会话密钥
     * @param string $appId 小程序应用ID
     * @return array
     */
    public static function decryptData(string $encryptedData,string $iv,string $sessionKey,string $appId) {
        $resArr = [
            'code' => 0
        ];

        if (strlen($iv) != 24) {
            $resArr['code'] = ErrorCode::WX_PARAM_ERROR;
            $resArr['message'] = '初始向量不合法';
            return $resArr;
        } else if (strlen($sessionKey) != 24) {
            $resArr['code'] = ErrorCode::WX_PARAM_ERROR;
            $resArr['message'] = '会话密钥不合法';
            return $resArr;
        }

        $aesIV = base64_decode($iv);
        $aesKey = base64_decode($sessionKey);
        $aesCipher = base64_decode($encryptedData);
        $decryptData = Tool::jsonDecode(openssl_decrypt($aesCipher, 'AES-128-CBC', $aesKey, 1, $aesIV));
        if (is_array($decryptData) && isset($decryptData['watermark']['appid']) && ($decryptData['watermark']['appid'] == $appId)) {
            $resArr['data'] = $decryptData;
        } else {
            $resArr['code'] = ErrorCode::WX_PARAM_ERROR;
            $resArr['message'] = '解密用户数据失败';
        }

        return $resArr;
    }

    /**
     * 获取小程序消息模板标题列表
     * @param string $appId
     * @param \Wx\Mini\MsgTemplateTitleList $templateList
     * @return array
     */
    public static function getMsgTemplateTitleList(string $appId,MsgTemplateTitleList $titleList){
        $resArr = [
            'code' => 0
        ];

        $url = self::$urlMsgTemplateTitleList . self::getAccessToken($appId);
        $getRes = self::sendPostReq($url, 'json', $titleList->getDetail());
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
     * 获取小程序消息模板标题关键词库
     * @param string $appId
     * @param string $titleId 模板标题id
     * @return array
     */
    public static function getMsgTemplateTitleKeywords(string $appId,string $titleId){
        $resArr = [
            'code' => 0
        ];

        $url = self::$urlMsgTemplateTitleKeywords . self::getAccessToken($appId);
        $getRes = self::sendPostReq($url, 'json', [
            'id' => $titleId,
        ]);
        $getData = Tool::jsonDecode($getRes);
        if(isset($getData['id'])){
            $resArr['data'] = $getData;
        } else {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $getData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 添加小程序消息模板
     * @param string $appId
     * @param \Wx\Mini\MsgTemplate $msgTemplate
     * @return array
     */
    public static function addMsgTemplate(string $appId,MsgTemplate $msgTemplate){
        $resArr = [
            'code' => 0
        ];

        $url = self::$urlAddMsgTemplate . self::getAccessToken($appId);
        $addRes = self::sendPostReq($url, 'json', $msgTemplate->getDetail());
        $addData = Tool::jsonDecode($addRes);
        if(isset($addData['template_id'])){
            $resArr['data'] = $addData;
        } else {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $addData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 获取小程序消息模板列表
     * @param string $appId
     * @param \Wx\Mini\MsgTemplateList $templateList
     * @return array
     */
    public static function getMsgTemplateList(string $appId,MsgTemplateList $templateList){
        $resArr = [
            'code' => 0
        ];

        $url = self::$urlMsgTemplateList . self::getAccessToken($appId);
        $getRes = self::sendPostReq($url, 'json', $templateList->getDetail());
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
     * @return array
     */
    public static function delMsgTemplate(string $appId,string $templateId){
        $resArr = [
            'code' => 0
        ];

        $url = self::$urlDelMsgTemplate . self::getAccessToken($appId);
        $delRes = self::sendPostReq($url, 'json', [
            'template_id' => $templateId,
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
}