<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/3/27 0027
 * Time: 9:04
 */
namespace Wx;

use Constant\ErrorCode;
use Constant\Server;
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
                $dataStr = self::arrayToXml($data);
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