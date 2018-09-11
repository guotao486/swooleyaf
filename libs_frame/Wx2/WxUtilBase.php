<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/11 0011
 * Time: 8:55
 */
namespace Wx2;

use Constant\ErrorCode;
use Exception\Wx\WxException;
use Tool\Tool;

abstract class WxUtilBase {
    const TYPE_SHOP = 'shop'; //类型-公众号
    const TYPE_MINI = 'mini'; //类型-小程序
    const TYPE_OPEN_SHOP = 'openshop'; //类型-第三方平台代理公众号
    const TYPE_OPEN_MINI = 'openmini'; //类型-第三方平台代理小程序
    const URL_QRCODE = 'http://paysdk.weixin.qq.com/example/qrcode.php?data=';

    public static $errorsShortUrl = [
        'XML_FORMAT_ERROR' => 'XML格式错误',
        'POST_DATA_EMPTY' => 'post数据为空',
        'LACK_PARAMS' => '缺少参数',
        'APPID_NOT_EXIST' => 'APPID不存在',
        'MCHID_NOT_EXIST' => 'MCHID不存在',
        'APPID_MCHID_NOT_MATCH' => 'appid和mch_id不匹配',
        'REQUIRE_POST_METHOD' => '请使用post方法',
        'SIGNERROR' => '签名错误',
    ];

    /**
     * 发送post请求
     * @param array $curlConfig
     * @return mixed
     * @throws \Exception\Wx\WxException
     */
    public static function sendPostReq(array $curlConfig) {
        $curlConfig[CURLOPT_POST] = true;
        $curlConfig[CURLOPT_RETURNTRANSFER] = true;
        if(!isset($curlConfig[CURLOPT_TIMEOUT_MS])){
            $curlConfig[CURLOPT_TIMEOUT_MS] = 2000;
        }
        if(!isset($curlConfig[CURLOPT_HEADER])){
            $curlConfig[CURLOPT_HEADER] = false;
        }
        if(!isset($curlConfig[CURLOPT_SSL_VERIFYPEER])){
            $curlConfig[CURLOPT_SSL_VERIFYPEER] = true;
        }
        if(!isset($curlConfig[CURLOPT_SSL_VERIFYHOST])){
            $curlConfig[CURLOPT_SSL_VERIFYHOST] = 2;
        }
        $sendRes = Tool::sendCurlReq($curlConfig);
        if ($sendRes['res_no'] == 0) {
            return $sendRes['res_content'];
        } else {
            throw new WxException('curl出错，错误码=' . $sendRes['res_no'], ErrorCode::WX_POST_ERROR);
        }
    }

    /**
     * 发送get请求
     * @param array $curlConfig
     * @return mixed
     * @throws \Exception\Wx\WxException
     */
    public static function sendGetReq(array $curlConfig) {
        $curlConfig[CURLOPT_SSL_VERIFYPEER] = false;
        $curlConfig[CURLOPT_SSL_VERIFYHOST] = false;
        $curlConfig[CURLOPT_HEADER] = false;
        $curlConfig[CURLOPT_RETURNTRANSFER] = true;
        if(!isset($curlConfig[CURLOPT_TIMEOUT_MS])){
            $curlConfig[CURLOPT_TIMEOUT_MS] = 2000;
        }
        $sendRes = Tool::sendCurlReq($curlConfig);
        if ($sendRes['res_no'] == 0) {
            return $sendRes['res_content'];
        } else {
            throw new WxException('curl出错，错误码=' . $sendRes['res_no'], ErrorCode::WX_GET_ERROR);
        }
    }
}