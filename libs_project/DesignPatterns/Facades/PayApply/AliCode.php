<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-5
 * Time: 上午12:47
 */
namespace DesignPatterns\Facades\PayApply;

use Ali\AliUtilPay;
use Ali\Pay\PayQrCode;
use Constant\ErrorCode;
use DesignPatterns\Facades\PayApplyFacade;
use Exception\Common\CheckException;
use Request\SyRequest;
use Tool\Tool;
use Traits\SimpleFacadeTrait;

class AliCode extends PayApplyFacade {
    use SimpleFacadeTrait;

    protected static function checkParams(array $data) : array {
        return [
            'a01_appid' => Tool::getConfig('project.' . SY_ENV . SY_PROJECT . '.alipay.appid.default'),
            'a01_timeout' => (string)SyRequest::getParams('a01_timeout', ''),
        ];
    }

    protected static function apply(array $data) : array {
        $pay = new PayQrCode($data['a01_appid']);
        $pay->setSubject($data['content_result']['pay_name']);
        $pay->setTotalAmount($data['content_result']['pay_money']);
        $pay->setAttach($data['content_result']['pay_attach']);
        $pay->setTimeoutExpress($data['a01_timeout']);
        $pay->setOutTradeNo($data['content_result']['pay_sn']);
        $payRes = AliUtilPay::sendServiceRequest($pay);
        unset($pay);
        if ($payRes['code'] > 0) {
            throw new CheckException($payRes['message'], ErrorCode::COMMON_PARAM_ERROR);
        }

        return [
            'qr_code' => $payRes['data']['qr_code'],
        ];
    }
}