<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-9
 * Time: 下午12:43
 */
namespace Map;

use Constant\ErrorCode;
use Log\Log;
use Tool\Tool;
use Traits\SimpleTrait;

abstract class MapUtilBase {
    use SimpleTrait;

    const TYPE_BAIDU = 'baidu';
    const TYPE_TENCENT = 'tencent';

    /**
     * 发送GET请求
     * @param string $url 请求地址
     * @param array $data 数据
     * @param string $mapType 地图类型
     * @param array $configs 配置数组
     * @return string|bool
     */
    protected static function sendGet(string $url,array $data,string $mapType,array $configs=[]) {
        $configs[CURLOPT_URL] = $url . '?' . http_build_query($data);
        $configs[CURLOPT_RETURNTRANSFER] = true;
        if($mapType == self::TYPE_TENCENT){
            $configs[CURLOPT_CUSTOMREQUEST] = 'POST';
        }
        if(!isset($configs[CURLOPT_TIMEOUT_MS])){
            $configs[CURLOPT_TIMEOUT_MS] = 1000;
        }
        if(!isset($configs[CURLOPT_HTTPHEADER])){
            $configs[CURLOPT_HTTPHEADER] = [];
        }
        $sendRes = Tool::sendCurlReq($configs);
        if($sendRes['res_no'] == 0){
            return $sendRes['res_content'];
        } else if($mapType == self::TYPE_BAIDU){
            Log::error('curl发送百度地图get请求出错,错误码=' . $sendRes['res_no'] . ',错误信息=' . $sendRes['res_msg'], ErrorCode::MAP_BAIDU_GET_ERROR);
            return false;
        } else {
            Log::error('curl发送腾讯地图get请求出错,错误码=' . $sendRes['res_no'] . ',错误信息=' . $sendRes['res_msg'], ErrorCode::MAP_TENCENT_GET_ERROR);
            return false;
        }
    }
}