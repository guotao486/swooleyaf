<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-16
 * Time: 2:08
 */
namespace SySms\Yun253;

use Constant\ErrorCode;
use DesignPatterns\Singletons\SmsConfigSingleton;
use Log\Log;
use Tool\Tool;
use Traits\SimpleTrait;

final class SmsUtil {
    use SimpleTrait;

    /**
     * 发送短信
     * @param \SySms\Yun253\SmsSend $smsSend 短信对象
     * @return array
     */
    public static function sendSms(SmsSend $smsSend) : array {
        $resArr = [
            'code' => 0
        ];

        $sendRes = Tool::sendCurlReq([
            CURLOPT_URL => SmsConfigSingleton::getInstance()->getYun253Config()->getAppUrlSend(),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_NOSIGNAL => true,
            CURLOPT_TIMEOUT_MS => 2000,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => Tool::jsonEncode($smsSend->getDetail(), JSON_UNESCAPED_UNICODE),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Expect:',
                'Content-Type: application/json',
            ],
        ]);
        if($sendRes['res_no'] > 0){
            Log::error('短信请求失败,curl错误码为' . $sendRes['res_no'], ErrorCode::SMS_POST_ERROR);

            $resArr['code'] = ErrorCode::SMS_POST_ERROR;
            $resArr['msg'] = '发送短信请求失败';
            return $resArr;
        }

        $resData = Tool::jsonDecode($sendRes['res_content']);
        if(!is_array($resData)){
            $resArr['code'] = ErrorCode::SMS_POST_ERROR;
            $resArr['msg'] = '解析请求数据失败';
            return $resArr;
        }
        if($resData['code'] == 0){
            $resArr['data'] = $resData;
        } else {
            $resArr['code'] = ErrorCode::SMS_POST_ERROR;
            $resArr['msg'] = $resData['errorMsg'];
        }

        return $resArr;
    }
}