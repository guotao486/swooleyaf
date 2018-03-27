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
    private static $chars = [
        '2', '3', '4', '5', '6', '7', '8', '9',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
        'i', 'j', 'k', 'm', 'n', 'p', 'q', 'r',
        's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
    ];

    /**
     * 数组转xml
     * @param array $data
     * @return string
     * @throws \Exception\Wx\WxException
     */
    public static function arrayToXml(array $data) : string {
        if (count($data) == 0) {
            throw new WxException('数组为空', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        $xml = '<xml>';
        foreach ($data as $key => $value) {
            if (is_numeric($value)) {
                $xml .= '<' . $key . '>' . $value . '</' . $key . '>';
            } else {
                $xml .= '<' . $key . '><![CDATA[' . $value . ']]></' . $key . '>';
            }
        }
        $xml .= '</xml>';

        return $xml;
    }

    /**
     * xml转数组
     * @param string $xml
     * @return array
     * @throws \Exception\Wx\WxException
     */
    public static function xmlToArray(string $xml) : array {
        if (strlen($xml . '') == 0) {
            throw new WxException('xml数据异常', ErrorCode::WX_PARAM_ERROR);
        }

        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $element = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $jsonStr = Tool::jsonEncode($element);
        return Tool::jsonDecode($jsonStr);
    }

    /**
     * 发送post请求
     * @param string|array $data 数据
     * @param string $url 请求地址
     * @param array $configs 配置数组
     * @param array $extends 扩展信息数组
     * @return mixed
     * @throws \Exception\Wx\WxException
     */
    protected static function sendPost(string $url, $data,array $configs=[],array &$extends=[]) {
        $dataStr = '';
        if(is_string($data)){
            $dataStr = $data;
        } else if(is_array($data)){
            $dataType = Tool::getArrayVal($configs, 'data_type', 'query');
            if($dataType == 'xml'){
                $dataStr = self::arrayToXml($data);
            } else if($dataType == 'json'){
                $dataStr = Tool::jsonEncode($data, JSON_UNESCAPED_UNICODE);
            } else if($dataType == 'query'){
                $dataStr = http_build_query($data);
            }
        }
        if(strlen($dataStr) == 0){
            throw new WxException('数据格式不合法', ErrorCode::WX_PARAM_ERROR);
        }

        $timeout = (int)Tool::getArrayVal($configs, 'timeout', 2000);

        $curlConfigs = [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $dataStr,
            CURLOPT_TIMEOUT_MS => $timeout,
            CURLOPT_HEADER => Tool::getArrayVal($configs, 'headers', false),
            CURLOPT_RETURNTRANSFER => true,
        ];
        if (Tool::getArrayVal($configs, 'ssl_verify', true)) { //是否需要ssl认证，默认需要
            $curlConfigs[CURLOPT_SSL_VERIFYPEER] = true;
            $curlConfigs[CURLOPT_SSL_VERIFYHOST] = 2; //严格校验
        } else {
            $curlConfigs[CURLOPT_SSL_VERIFYPEER] = false;
            $curlConfigs[CURLOPT_SSL_VERIFYHOST] = false;
        }

        if (Tool::getArrayVal($configs, 'use_cert', false)) { //是否需要证书，默认不需要
            $curlConfigs[CURLOPT_SSLCERTTYPE] = 'PEM';
            $curlConfigs[CURLOPT_SSLCERT] = Tool::getArrayVal($configs, 'sslcert_path', '');
            $curlConfigs[CURLOPT_SSLKEYTYPE] = 'PEM';
            $curlConfigs[CURLOPT_SSLKEY] = Tool::getArrayVal($configs, 'sslkey_path', '');
        }
        $sendRes = Tool::sendCurlReq($curlConfigs);
        if ($sendRes['res_no'] == 0) {
            return $sendRes['res_content'];
        } else {
            throw new WxException('curl出错，错误码=' . $sendRes['res_no'], ErrorCode::WX_POST_ERROR);
        }
    }

    /**
     * 发送get请求
     * @param string $url 请求地址
     * @param int $timeout 执行超时时间,单位为毫秒，默认为2s
     * @param array $extends 扩展信息数组
     * @return mixed
     * @throws \Exception\Wx\WxException
     */
    protected static function sendGetReq(string $url,int $timeout=2000,array &$extends=[]) {
        $sendRes = Tool::sendCurlReq([
            CURLOPT_URL => $url,
            CURLOPT_TIMEOUT_MS => $timeout,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        if ($sendRes['res_no'] == 0) {
            return $sendRes['res_content'];
        } else {
            throw new WxException('curl出错，错误码=' . $sendRes['res_no'], ErrorCode::WX_GET_ERROR);
        }
    }

    /**
     * 生成随机字符串
     * @param int $length 需要获取的随机字符串长度
     * @return string
     */
    public static function createNonceStr(int $length=32) : string {
        $resStr = '';
        for ($i = 0; $i < $length; $i++) {
            $resStr .= self::$chars[mt_rand(0, 31)];
        }

        return $resStr;
    }
}