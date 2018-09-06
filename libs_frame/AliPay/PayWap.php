<?php
/**
 * 手机网站支付
 * User: Administrator
 * Date: 2017-04-08
 * Time: 23:50
 */
namespace AliPay;

use Constant\ErrorCode;
use DesignPatterns\Singletons\AliConfigSingleton;
use Exception\Ali\AliPayException;

class PayWap extends BaseTrade {
    /**
     * 跳转url地址
     * @var string
     */
    private $return_url = '';

    /**
     * 跳转基础url地址
     * @var string
     */
    private $return_baseurl = '';

    /**
     * 消息通知url地址
     * @var string
     */
    private $notify_url = '';

    /**
     * 交易的具体描述信息
     * @var string
     */
    private $body = '';

    /**
     * 商品的标题
     * @var string
     */
    private $subject = '';

    /**
     * 商户网站唯一订单号
     * @var string
     */
    private $out_trade_no = '';

    /**
     * 该笔订单允许的最晚付款时间，逾期将关闭交易,取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天
     * @var string
     */
    private $timeout_express = '';

    /**
     * 订单总金额，单位为元，精确到小数点后两位
     * @var string
     */
    private $total_amount = '';

    /**
     * 收款支付宝用户ID
     * @var string
     */
    private $seller_id = '';

    /**
     * 销售产品码
     * @var string
     */
    private $product_code = '';

    /**
     * 商品主类型：0—虚拟类商品，1—实物类商品
     * @var string
     */
    private $goods_type = '';

    public function __construct(string $appId) {
        parent::__construct($appId);
        $payConfig = AliConfigSingleton::getInstance()->getPayConfig($appId);
        $this->method = 'alipay.trade.wap.pay';
        $this->notify_url = $payConfig->getUrlNotify();
        $this->return_baseurl = $payConfig->getUrlReturn();
        $this->setBizContent('seller_id', $payConfig->getSellerId());
        $this->setBizContent('product_code', 'QUICK_WAP_PAY');
        $this->setBizContent('goods_type', '1');
    }

    private function __clone(){
    }

    /**
     * @param string $returnUrl
     * @throws \Exception\Ali\AliPayException
     */
    public function setReturnUrl(string $returnUrl) {
        if(preg_match('/^(http|https)\:\/\/\S+$/', $returnUrl) > 0) {
            $this->return_url = $this->return_baseurl . urlencode($returnUrl);
        } else {
            throw new AliPayException('同步通知地址不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $subject
     * @throws \Exception\Ali\AliPayException
     */
    public function setSubject(string $subject) {
        if (strlen($subject) > 0) {
            $this->setBizContent('subject', mb_substr($subject, 0, 80));
        } else {
            throw new AliPayException('商品标题不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
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
     * @param string $timeoutExpress
     */
    public function setTimeoutExpress(string $timeoutExpress) {
        if (strlen($timeoutExpress) > 0) {
            $this->setBizContent('timeout_express', $timeoutExpress);
        }
    }

    /**
     * @param int $totalAmount
     * @throws \Exception\Ali\AliPayException
     */
    public function setTotalAmount(int $totalAmount) {
        if ($totalAmount > 0) {
            $this->setBizContent('total_amount', number_format(($totalAmount / 100), 2, '.', ''));
        } else {
            throw new AliPayException('订单总金额必须大于0', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $attach
     * @throws \Exception\Ali\AliPayException
     */
    public function setAttach(string $attach) {
        $length = strlen($attach);
        if ($length > 128) {
            throw new AliPayException('附加数据不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        } else if ($length > 0) {
            $this->setBizContent('body', $attach);
        }
    }

    public function getDetail() : array {
        if (strlen($this->return_url) == 0) {
            throw new AliPayException('同步通知地址不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        $bizContent = $this->getBizContent();
        if (!isset($bizContent['subject'])) {
            throw new AliPayException('商品标题不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if (!isset($bizContent['out_trade_no'])) {
            throw new AliPayException('商户订单号不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if (!isset($bizContent['total_amount'])) {
            throw new AliPayException('订单总金额不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        $resArr = $this->getContentArr();
        $resArr['notify_url'] = $this->notify_url;
        $resArr['return_url'] = $this->return_url;
        $resArr['sign'] = TradeUtil::createSign($resArr, $resArr['sign_type']);

        return $resArr;
    }
}