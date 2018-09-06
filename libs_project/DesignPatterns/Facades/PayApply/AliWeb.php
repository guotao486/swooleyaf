<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-5
 * Time: 上午12:50
 */
namespace DesignPatterns\Facades\PayApply;

use Ali\AliUtilPay;
use Ali\Pay\PayWap;
use Constant\ErrorCode;
use DesignPatterns\Facades\PayApplyFacade;
use Exception\Common\CheckException;
use Request\SyRequest;
use Tool\Tool;
use Traits\SimpleFacadeTrait;

class AliWeb extends PayApplyFacade {
    use SimpleFacadeTrait;

    protected static function checkParams(array $data) : array {
        $returnUrl = (string)SyRequest::getParams('a01_returnurl', '');
        if(strlen($returnUrl) == 0){
            throw new CheckException('同步通知链接不能为空', ErrorCode::COMMON_PARAM_ERROR);
        }

        return [
            'a01_appid' => Tool::getConfig('project.' . SY_ENV . SY_PROJECT . '.alipay.appid.default'),
            'a01_timeout' => (string)SyRequest::getParams('a01_timeout', ''),
            'a01_returnurl' => $returnUrl,
        ];
    }

    protected static function apply(array $data) : array {
        $pay = new PayWap($data['a01_appid']);
        $pay->setReturnUrl($data['a01_returnurl']);
        $pay->setSubject($data['content_result']['pay_name']);
        $pay->setTotalAmount($data['content_result']['pay_money']);
        $pay->setAttach($data['content_result']['pay_attach']);
        $pay->setTimeoutExpress($data['a01_timeout']);
        $pay->setOutTradeNo($data['content_result']['pay_sn']);
        $html = AliUtilPay::createWapPayHtml($pay);
        unset($pay);

        return [
            'html' => $html,
        ];
    }
}