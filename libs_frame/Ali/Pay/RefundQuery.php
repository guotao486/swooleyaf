<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/6 0006
 * Time: 16:24
 */
namespace Ali\Pay;

use Ali\AliBase;
use Ali\AliUtilBase;
use Constant\ErrorCode;
use Exception\Ali\AliPayException;

class RefundQuery extends AliBase {
    /**
     * 商户订单号
     * @var string
     */
    private $out_trade_no = '';

    /**
     * 支付宝交易号
     * @var string
     */
    private $trade_no = '';

    /**
     * 退款单号
     * @var string
     */
    private $out_request_no = '';

    public function __construct(string $appId) {
        parent::__construct($appId);
        $this->setMethod('alipay.trade.fastpay.refund.query');
    }

    private function __clone(){
    }

    /**
     * @param string $outTradeNo
     * @throws \Exception\Ali\AliPayException
     */
    public function setOutTradeNo(string $outTradeNo) {
        if (ctype_digit($outTradeNo)) {
            $this->setBizContent('out_trade_no', $outTradeNo);
        } else {
            throw new AliPayException('商户订单号不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $tradeNo
     * @throws \Exception\Ali\AliPayException
     */
    public function setTradeNo(string $tradeNo) {
        if (ctype_digit($tradeNo)) {
            $this->setBizContent('trade_no', $tradeNo);
        } else {
            throw new AliPayException('支付宝交易号不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $refundNo
     * @throws \Exception\Ali\AliPayException
     */
    public function setRefundNo(string $refundNo) {
        if (ctype_digit($refundNo)) {
            $this->setBizContent('out_request_no', $refundNo);
        } else {
            throw new AliPayException('退款单号不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        $bizContent = $this->getBizContent();
        if ((!isset($bizContent['out_trade_no'])) && (!isset($bizContent['trade_no']))) {
            throw new AliPayException('商户订单号和支付宝交易号不能都为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if (!isset($bizContent['out_request_no'])) {
            throw new AliPayException('退款单号不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        $resArr = $this->getContentArr();
        $resArr['sign'] = AliUtilBase::createSign($resArr, $resArr['sign_type']);
        return $resArr;
    }
}