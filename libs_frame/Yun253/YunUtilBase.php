<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/8 0008
 * Time: 15:07
 */
namespace Yun253;

use Constant\ErrorCode;
use Log\Log;
use Tool\Tool;
use Traits\SimpleTrait;

abstract class YunUtilBase {
    use SimpleTrait;

    /**
     * @param string $url
     * @param array $data
     * @param array $curlConfig
     * @return string|bool
     * @throws \Exception\Yun253\SmsException
     */
    protected static function sendPostReq(string $url,array $data,array $curlConfig=[]) {
        $curlConfig[CURLOPT_URL] = $url;
        $curlConfig[CURLOPT_SSL_VERIFYPEER] = false;
        $curlConfig[CURLOPT_SSL_VERIFYHOST] = false;
        $curlConfig[CURLOPT_NOSIGNAL] = true;
        $curlConfig[CURLOPT_POST] = true;
        $curlConfig[CURLOPT_POSTFIELDS] = Tool::jsonEncode($data, JSON_UNESCAPED_UNICODE);
        $curlConfig[CURLOPT_RETURNTRANSFER] = true;
        $curlConfig[CURLOPT_HTTPHEADER] = [
            'Content-Type: application/json',
            'Expect:',
        ];
        if(!isset($curlConfig[CURLOPT_TIMEOUT_MS])){
            $curlConfig[CURLOPT_TIMEOUT_MS] = 2000;
        }

        $sendRes = Tool::sendCurlReq($curlConfig);
        if ($sendRes['res_no'] == 0) {
            return $sendRes['res_content'];
        } else {
            Log::error('请求失败,curl错误码为' . $sendRes['res_no'], ErrorCode::SMS_POST_ERROR);
            return false;
        }
    }

    /**
     * 发送服务请求
     * @param \Yun253\YunBase $yunBase
     * @return array
     */
    public static function sendServiceRequest(YunBase $yunBase){
        $resArr = [
            'code' => 0
        ];

        $sendRes = self::sendPostReq($yunBase->getServiceUrl(), $yunBase->getDetail());
        if($sendRes === false){
            $resArr['code'] = ErrorCode::SMS_POST_ERROR;
            $resArr['msg'] = '发送短信请求失败';
            return $resArr;
        }

        $sendData = Tool::jsonDecode($sendRes);
        if(isset($sendData['code']) && ($sendData['code'] == 0)){
            $resArr['data'] = $sendData;
        } else if(isset($sendData['errorMsg'])){
            $resArr['code'] = ErrorCode::SMS_POST_ERROR;
            $resArr['msg'] = $sendData['errorMsg'];
        } else {
            $resArr['code'] = ErrorCode::SMS_POST_ERROR;
            $resArr['msg'] = '解析请求数据失败';
        }

        return $resArr;
    }
}