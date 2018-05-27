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
     * @api {post} /Index/PayNotify/handleWxPayNotify 处理微信支付通知
     * @apiDescription 处理微信支付通知
     * @apiGroup PayNotify
     */
    public function handleWxPayNotifyAction() {
        $wxMsg = \Tool\Tool::getArrayVal($GLOBALS, 'HTTP_RAW_POST_DATA', '');
        \Log\Log::log('wx pay data:' . $wxMsg);
        $xmlData = \Wx\WxUtilShop::xmlToArray($wxMsg);
        if (\Wx\WxUtilShop::checkSign($xmlData, $xmlData['appid'])) {
            $handleRes = \SyModule\SyModuleOrder::getInstance()->sendApiReq('/Index/Pay/handleWxPayNotify', $xmlData);
            $handleData = \Tool\Tool::jsonDecode($handleRes);
            if(is_array($handleData) && isset($handleData['code']) && ($handleData['code'] == \Constant\ErrorCode::COMMON_SUCCESS)){
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
     * @api {post} /Index/PayNotify/handleWxPrePayNotify 处理微信扫码预支付通知
     * @apiDescription 处理微信扫码预支付通知
     * @apiGroup PayNotify
     */
    public function handleWxPrePayNotifyAction() {
        $wxMsg = \Tool\Tool::getArrayVal($GLOBALS, 'HTTP_RAW_POST_DATA', '');
        \Log\Log::log('wx pre pay data:' . $wxMsg);
        $xmlData = \Wx\WxUtilShop::xmlToArray($wxMsg);
        if (\Wx\WxUtilShop::checkSign($xmlData, $xmlData['appid'])) {
            $handleRes = \SyModule\SyModuleOrder::getInstance()->sendApiReq('/Index/Pay/handleWxPrePayNotify', $xmlData);
            $resData = \Tool\Tool::jsonDecode($handleRes);
            if (is_array($resData) && isset($resData['code']) && ($resData['code'] == \Constant\ErrorCode::COMMON_SUCCESS)) {
                $this->sendRsp($resData['data']['result']);
            } else {
                $this->sendRsp('');
            }
        } else {
            $this->sendRsp('');
        }
    }

    /**
     * 处理支付宝付款异步通知消息
     * @api {post} /Index/PayNotify/handleAliPayNotify 处理支付宝付款异步通知消息
     * @apiDescription 处理支付宝付款异步通知消息
     * @apiGroup PayNotify
     * @apiSuccess HandleSuccess 处理成功
     * @apiSuccessExample success:
     *     success
     * @apiSuccess HandleFail 处理失败
     * @apiSuccessExample fail:
     *     fail
     */
    public function handleAliPayNotifyAction() {
        $resultMsg = 'fail';
        $allParams = \Request\SyRequest::getParams();
        \Log\Log::log('ali pay data:' . \Tool\Tool::jsonEncode($allParams, JSON_UNESCAPED_UNICODE));
        if(\AliPay\TradeUtil::verifyData($allParams, '2', 'RSA2')){
            $handleRes = \SyModule\SyModuleOrder::getInstance()->sendApiReq('/Index/Pay/handleAliPayNotify', $allParams);
            $handleData = \Tool\Tool::jsonDecode($handleRes);
            if(is_array($handleData) && isset($handleData['code']) && ($handleData['code'] == \Constant\ErrorCode::COMMON_SUCCESS)){
                $resultMsg = 'success';
            }
        }

        $this->sendRsp($resultMsg);
    }

    /**
     * 处理支付宝网页支付同步回跳地址
     * @api {get} /Index/PayNotify/handleAliWebRedirect 处理支付宝网页支付同步回跳地址
     * @apiDescription 处理支付宝网页支付同步回跳地址
     * @apiGroup PayNotify
     * @apiParam {string} url 同步回跳URL地址
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
     */
    public function handleAliWebRedirectAction() {
        $expireTime = time() + 604800;
        $sessionId = \Tool\SySession::getSessionId();
        $redirectUrl = \Request\SyRequest::getParams('url');
        \Response\SyResponseHttp::cookie(\Constant\Project::DATA_KEY_SESSION_TOKEN, $sessionId, $expireTime, '/', \SyServer\HttpServer::getServerConfig('cookiedomain_base', ''));
        \Response\SyResponseHttp::redirect($redirectUrl);

        $this->sendRsp();
    }

    /**
     * 处理支付宝退款异步通知消息
     * @api {post} /Index/PayNotify/handleAliRefundNotify 处理支付宝退款异步通知消息
     * @apiDescription 处理支付宝退款异步通知消息
     * @apiGroup PayNotify
     * @apiSuccess HandleSuccess 处理成功
     * @apiSuccessExample success:
     *     success
     * @apiSuccess HandleFail 处理失败
     * @apiSuccessExample fail:
     *     fail
     */
    public function handleAliRefundNotifyAction() {
        $resultMsg = 'fail';
        $allParams = \Request\SyRequest::getParams();
        \Log\Log::log('ali refund data:' . \Tool\Tool::jsonEncode($allParams, JSON_UNESCAPED_UNICODE));
        if(\AliPay\TradeUtil::verifyData($allParams, '2', 'RSA2')){
            $handleRes = \SyModule\SyModuleOrder::getInstance()->sendApiReq('/Index/Pay/handleAliRefundNotify', $allParams);
            $handleData = \Tool\Tool::jsonDecode($handleRes);
            if(is_array($handleData) && isset($handleData['code']) && ($handleData['code'] == \Constant\ErrorCode::COMMON_SUCCESS)){
                $resultMsg = 'success';
            }
        }

        $this->sendRsp($resultMsg);
    }
}