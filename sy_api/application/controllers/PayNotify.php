<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 18-3-9
 * Time: 下午11:06
 */
class PayNotifyController extends CommonController {
    public $signStatus = false;

    public function init() {
        parent::init();
        $this->signStatus = false;
    }

    /**
     * 处理微信支付通知
     */
    public function handleWxPayNotifyAction() {
        $wxMsg = \Tool\Tool::getArrayVal($GLOBALS, 'HTTP_RAW_POST_DATA', '');
        \Log\Log::log('wx pay data:' . $wxMsg);
        $xmlData = \Wx\WxUtil::xmlToArray($wxMsg);
        if (\Wx\WxUtil::checkSign($xmlData, $xmlData['appid'])) {
            $handleRes = \SyModule\SyModuleOrder::getInstance()->sendApiReq('/Index/Pay/handleWxPayNotify', $xmlData);
            $handleData = \Tool\Tool::jsonDecode($handleRes);
            if(is_array($handleData) && isset($handleData['code']) && ($handleData['code'] == 0)){
                $error = '';
            } else {
                $error = '处理失败';
            }
        } else {
            $error = '签名失败';
        }

        $resXml = '<xml>';
        if (strlen($error) > 0) {
            $resXml .= '<return_code>FAIL</return_code><return_msg>' . $error . '</return_msg>';
        } else {
            $resXml .= '<return_code>SUCCESS</return_code>';
        }
        $resXml .= '</xml>';

        $this->sendRsp($resXml);
    }

    /**
     * 处理微信扫码预支付通知
     */
    public function handleWxPrePayNotifyAction() {
        $wxMsg = \Tool\Tool::getArrayVal($GLOBALS, 'HTTP_RAW_POST_DATA', '');
        \Log\Log::log('wx pre pay data:' . $wxMsg);
        $xmlData = \Wx\WxUtil::xmlToArray($wxMsg);
        if (\Wx\WxUtil::checkSign($xmlData, $xmlData['appid'])) {
            $handleRes = \SyModule\SyModuleOrder::getInstance()->sendApiReq('/Index/Pay/handleWxPrePayNotify', $xmlData);
            $resData = \Tool\Tool::jsonDecode($handleRes);
        } else {
            $this->sendRsp('');
        }

        $this->sendRsp($resData['data']);
    }

    /**
     * 处理支付宝网页支付同步回跳地址
     * @api {get} /Index/Pay/handleAliWebRedirect 处理支付宝网页支付同步回跳地址
     * @apiDescription 处理支付宝网页支付同步回跳地址
     * @apiGroup OrderPay
     * @apiParam {string} url 同步回跳URL地址
     * @apiParam {string} _sytoken 令牌标识
     * @apiSuccess HandleSuccess 处理成功
     * @apiSuccessExample success:
     *     HTTP/1.1 302
     *     {
     *         "Location": "http://www.baidu.com"
     *     }
     * @apiSuccess HandleFail 处理失败
     * @apiSuccessExample fail:
     *     跳转地址不正确
     * @SyFilter-{"field": "url","explain": "同步回跳URL地址","type": "string","rules": {"required": 1,"url": 1}}
     * @SyFilter-{"field": "_sytoken","explain": "令牌标识","type": "string","rules": {"required": 1,"min": 1}}
     */
    public function handleAliWebRedirectAction() {
        $expireTime = time() + 604800;
        $allParams = \Request\SyRequest::getParams();
        \Response\SyResponseHttp::cookie('token', $allParams['_sytoken'], $expireTime, '/', \SyServer\HttpServer::getServerConfig('cookiedomain_base', ''));
        \Response\SyResponseHttp::redirect($allParams['url']);

        $this->sendRsp();
    }
}