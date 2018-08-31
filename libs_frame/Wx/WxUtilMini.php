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
use Wx\Mini\Qrcode;

class WxUtilMini extends WxUtilAloneBase {
    use SimpleTrait;

    private static $urlAuthorizeMiniProgram = 'https://api.weixin.qq.com/sns/jscode2session?grant_type=authorization_code&appid=';
    private static $urlMiniProgramQrcode = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=';

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
        $url = self::$urlAuthorizeMiniProgram . $shopConfig->getAppId() . '&secret=' . $shopConfig->getSecret() . '&js_code=' . $code;
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

        $url = self::$urlMiniProgramQrcode . self::getAccessToken($appId);
        $getRes = self::sendPostReq($url, 'json', $qrcode->getDetail());
        $getData = Tool::jsonDecode($getRes);
        if(is_array($getData)){
            $resArr['code'] = ErrorCode::WX_PARAM_ERROR;
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
}