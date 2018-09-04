<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/3/27 0027
 * Time: 9:04
 */
namespace Wx;

use Constant\ErrorCode;
use Exception\Wx\WxException;
use Tool\Tool;

abstract class WxUtilBase {
    const TYPE_SHOP = 'shop'; //类型-公众号
    const TYPE_MINI = 'mini'; //类型-小程序
    const TYPE_OPEN_SHOP = 'openshop'; //类型-第三方平台代理公众号
    const TYPE_OPEN_MINI = 'openmini'; //类型-第三方平台代理小程序

    /**
     * 发送post请求
     * @param string $url 请求地址
     * @param string $dataType 数据类型
     * @param string|array $data 数据
     * @param array $curlConfig curl配置数组
     * @return mixed
     * @throws \Exception\Wx\WxException
     */
    protected static function sendPostReq(string $url,string $dataType, $data,array $curlConfig=[]) {
        switch ($dataType) {
            case 'string' :
                $dataStr = $data;
                break;
            case 'json' :
                $dataStr = Tool::jsonEncode($data, JSON_UNESCAPED_UNICODE);
                break;
            case 'xml' :
                $dataStr = Tool::arrayToXml($data);
                break;
            case 'query' :
                $dataStr = http_build_query($data);
                break;
            default :
                $dataStr = '';
        }
        if((!is_string($dataStr)) || (strlen($dataStr) == 0)){
            throw new WxException('数据格式不合法', ErrorCode::WX_PARAM_ERROR);
        }

        $curlConfig[CURLOPT_URL] = $url;
        $curlConfig[CURLOPT_POST] = true;
        $curlConfig[CURLOPT_POSTFIELDS] = $dataStr;
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
     * @param string $url 请求地址
     * @param array $curlConfig curl配置数组
     * @return mixed
     * @throws \Exception\Wx\WxException
     */
    protected static function sendGetReq(string $url,array $curlConfig=[]) {
        $curlConfig[CURLOPT_URL] = $url;
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