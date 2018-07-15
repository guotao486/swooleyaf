<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-7-15
 * Time: 上午9:37
 */
namespace Wx;

use Constant\ErrorCode;
use Tool\Tool;
use Traits\SimpleTrait;

final class WxUtilOpenShop extends WxUtilOpenBase {
    use SimpleTrait;

    private static $urlSendCustom = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=';

    /**
     * 发送客服消息
     * @param array $data 消息数据
     * @param string $appId 授权公众号app id
     * @return array
     */
    public static function sendCustomMsg(array $data,string $appId) : array {
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlSendCustom . self::getAuthorizerAccessToken($appId);
        $sendRes = self::sendPostReq($url, 'json', $data, [
            CURLOPT_HEADER => [
                'Expect:',
            ],
        ]);
        $resData = Tool::jsonDecode($sendRes);
        if ($resData['errcode'] == 0) {
            $resArr['data'] = $resData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $resData['errmsg'];
        }

        return $resArr;
    }
}