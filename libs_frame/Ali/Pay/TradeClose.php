<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/6 0006
 * Time: 16:06
 */
namespace Ali\Pay;

use Ali\AliBase;
use Ali\AliUtilBase;
use Constant\ErrorCode;
use DesignPatterns\Singletons\AliConfigSingleton;
use Exception\Ali\AliPayException;

class TradeClose extends AliBase {
    /**
     * 支付宝服务器主动通知商户服务器里指定的页面http/https路径
     * @var string
     */
    private $notify_url = '';

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

    public function __construct(string $appId) {
        parent::__construct($appId);
        $this->notify_url = AliConfigSingleton::getInstance()->getPayConfig($appId)->getUrlNotify();
        $this->setMethod('alipay.trade.close');
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

    public function getDetail() : array {
        $bizContent = $this->getBizContent();
        if ((!isset($bizContent['out_trade_no'])) && (!isset($bizContent['trade_no']))) {
            throw new AliPayException('商户订单号和支付宝交易号不能都为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        $resArr = $this->getContentArr();
        $resArr['notify_url'] = $this->notify_url;
        $resArr['sign'] = AliUtilBase::createSign($resArr, $resArr['sign_type']);
        return $resArr;
    }
}