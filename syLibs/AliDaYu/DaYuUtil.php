<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-16
 * Time: 2:08
 */
namespace AliDaYu;

use Constant\ErrorCode;
use DesignPatterns\Singletons\AliConfigSingleton;
use Log\Log;
use Tool\Tool;
use Traits\SimpleTrait;

final class DaYuUtil {
    use SimpleTrait;

    private static $urlHttp = 'http://gw.api.taobao.com/router/rest';

    /**
     * 生成短信签名字符串
     * @param array $data 参数数组
     * @return void
     */
    public static function createSmsSign(array &$data){
        $appSecret = AliConfigSingleton::getInstance()->getDaYuConfig()->getAppSecret();
        unset($data['sign']);
        ksort($data);
        $needStr = $appSecret;
        foreach ($data as $key => $value) {
            $needStr .= $key . $value;
        }
        $needStr .= $appSecret;
        $data['sign'] = strtoupper(md5($needStr));
    }

    /**
     * 发送短信
     * @param DaYuSmsSend $smsSend 短信对象
     * @return array
     */
    public static function sendSms(DaYuSmsSend $smsSend) : array {
        $resArr = [
            'code' => 0
        ];

        $reqData = $smsSend->getDetail();
        $res = self::sendPostReq(self::$urlHttp, $reqData);
        $resData = Tool::jsonDecode($res);
        if (isset($resData['alibaba_aliqin_fc_sms_num_send_response'])) {
            $resArr['data'] = $resData['alibaba_aliqin_fc_sms_num_send_response'];
        } else {
            $errorStr = Tool::jsonEncode($resData['error_response'], JSON_UNESCAPED_UNICODE);
            Log::error($errorStr, ErrorCode::ALIDAYU_POST_ERROR);
            $resArr['code'] = ErrorCode::ALIDAYU_POST_ERROR;
            $resArr['msg'] = $errorStr;
        }

        return $resArr;
    }

    /**
     * 查询短信
     * @param \AliDaYu\DaYuSmsQuery $smsQuery
     * @return array
     */
    public static function querySms(DaYuSmsQuery $smsQuery){
        $resArr = [
            'code' => 0
        ];

        $reqData = $smsQuery->getDetail();
        $res = self::sendPostReq(self::$urlHttp, $reqData);
        $resData = Tool::jsonDecode($res);
        if (isset($resData['alibaba_aliqin_fc_sms_num_query_response'])) {
            $resArr['data'] = $resData['alibaba_aliqin_fc_sms_num_query_response'];
        } else {
            $errorStr = Tool::jsonEncode($resData['error_response'], JSON_UNESCAPED_UNICODE);
            Log::error($errorStr, ErrorCode::ALIDAYU_POST_ERROR);
            $resArr['code'] = ErrorCode::ALIDAYU_POST_ERROR;
            $resArr['msg'] = $errorStr;
        }

        return $resArr;
    }

    /**
     * 发送POST请求
     * @param string $url 请求地址
     * @param array $data 请求参数
     * @param array $curlConfig curl配置数组
     * @return mixed
     */
    private static function sendPostReq(string $url,array $data,array $curlConfig=[]){
        $curlConfig[CURLOPT_URL] = $url;
        $curlConfig[CURLOPT_NOSIGNAL] = true;
        $curlConfig[CURLOPT_SSL_VERIFYPEER] = false;
        $curlConfig[CURLOPT_SSL_VERIFYHOST] = false;
        $curlConfig[CURLOPT_POST] = true;
        $curlConfig[CURLOPT_POSTFIELDS] = http_build_query($data);
        $curlConfig[CURLOPT_RETURNTRANSFER] = true;
        $curlConfig[CURLOPT_HTTPHEADER] = [
            'Expect:',
        ];
        if(!isset($curlConfig[CURLOPT_TIMEOUT_MS])){
            $curlConfig[CURLOPT_TIMEOUT_MS] = 1000;
        }
        $sendRes = Tool::sendCurlReq($curlConfig);
        if($sendRes['res_no'] > 0){
            Log::error('短信请求失败,curl错误码为' . $sendRes['res_no'], ErrorCode::ALIDAYU_POST_ERROR);
        }

        return $sendRes['res_content'];
    }
}