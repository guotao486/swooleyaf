<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-7
 * Time: 下午11:26
 */
namespace Ali\Pay;

use Ali\AliBase;
use Constant\ErrorCode;
use DesignPatterns\Singletons\AliConfigSingleton;
use Exception\Ali\AliPayException;
use Tool\Tool;

class TradePay extends AliBase {
    private $sceneList = [
        'bar_code' => 1,
        'wave_code' => 1,
    ];
    private $authConfirmModeList = [
        'COMPLETE' => 1,
        'NOT_COMPLETE' => 1,
    ];

    /**
     * 商户订单号
     * @var string
     */
    private $out_trade_no = '';
    /**
     * 支付场景
     * @var string
     */
    private $scene = '';
    /**
     * 支付授权码
     * @var string
     */
    private $auth_code = '';
    /**
     * 销售产品码
     * @var string
     */
    private $product_code = '';
    /**
     * 订单标题
     * @var string
     */
    private $subject = '';
    /**
     * 买家支付宝用户ID
     * @var string
     */
    private $buyer_id = '';
    /**
     * 卖家支付宝用户ID
     * @var string
     */
    private $seller_id = '';
    /**
     * 标价币种
     * @var string
     */
    private $trans_currency = '';
    /**
     * 结算币种
     * @var string
     */
    private $settle_currency = '';
    /**
     * 订单总金额,单位为分
     * @var int
     */
    private $total_amount = 0;
    /**
     * 可打折金额金额,单位为分
     * @var int
     */
    private $discountable_amount = 0;
    /**
     * 订单描述
     * @var string
     */
    private $body = '';
    /**
     * 商品信息
     * @var array
     */
    private $goods_detail = [];
    /**
     * 操作员编号
     * @var string
     */
    private $operator_id = '';
    /**
     * 门店编号
     * @var string
     */
    private $store_id = '';
    /**
     * 终端编号
     * @var string
     */
    private $terminal_id = '';
    /**
     * 业务扩展参数
     * @var array
     */
    private $extend_params = [];
    /**
     * 允许的最晚付款时间
     * @var string
     */
    private $timeout_express = '';
    /**
     * 预授权确认模式
     * @var string
     */
    private $auth_confirm_mode = '';
    /**
     * 终端设备相关信息
     * @var array
     */
    private $terminal_params = [];

    public function __construct(string $appId){
        parent::__construct($appId);
        $payConfig = AliConfigSingleton::getInstance()->getPayConfig($appId);
        $this->notify_url = $payConfig->getUrlNotify();
        $this->scene = 'bar_code';
        $this->trans_currency = 'CNY';
        $this->settle_currency = 'CNY';
        $this->setMethod('alipay.trade.pay');
        $this->setBizContent('seller_id', $payConfig->getSellerId());
    }

    public function __clone(){
    }

    /**
     * @param string $outTradeNo
     * @throws \Exception\Ali\AliPayException
     */
    public function setOutTradeNo(string $outTradeNo){
        if(ctype_digit($outTradeNo)){
            $this->setBizContent('out_trade_no', $outTradeNo);
        } else {
            throw new AliPayException('商户订单号不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $scene
     * @throws \Exception\Ali\AliPayException
     */
    public function setScene(string $scene){
        if(isset($this->sceneList[$scene])){
            $this->setBizContent('scene', $scene);
        } else {
            throw new AliPayException('支付场景不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $authCode
     * @throws \Exception\Ali\AliPayException
     */
    public function setAuthCode(string $authCode){
        if(ctype_digit($authCode)){
            $this->setBizContent('auth_code', $authCode);
        } else {
            throw new AliPayException('支付授权码不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $productCode
     * @throws \Exception\Ali\AliPayException
     */
    public function setProductCode(string $productCode){
        if(ctype_alnum($productCode)){
            $this->setBizContent('product_code', $productCode);
        } else {
            throw new AliPayException('销售产品码不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $subject
     * @throws \Exception\Ali\AliPayException
     */
    public function setSubject(string $subject){
        $title = mb_substr(trim($subject), 0, 128);
        if(strlen($title) > 0){
            $this->setBizContent('subject', $title);
        } else {
            throw new AliPayException('订单标题不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $buyerId
     * @throws \Exception\Ali\AliPayException
     */
    public function setBuyerId(string $buyerId){
        if(ctype_digit($buyerId)){
            $this->setBizContent('buyer_id', $buyerId);
        } else {
            throw new AliPayException('买家支付宝用户ID不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $transCurrency
     * @throws \Exception\Ali\AliPayException
     */
    public function setTransCurrency(string $transCurrency){
        if(ctype_alpha($transCurrency)){
            $this->setBizContent('trans_currency', $transCurrency);
        } else {
            throw new AliPayException('标价币种不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $settleCurrency
     * @throws \Exception\Ali\AliPayException
     */
    public function setSettleCurrency(string $settleCurrency){
        if(ctype_alpha($settleCurrency)){
            $this->setBizContent('settle_currency', $settleCurrency);
        } else {
            throw new AliPayException('结算币种不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param int $totalAmount
     * @throws \Exception\Ali\AliPayException
     */
    public function setTotalAmount(int $totalAmount){
        if(($totalAmount > 0) && ($totalAmount <= 10000000000)){
            $this->total_amount = $totalAmount;
            $this->setBizContent('total_amount', number_format(($totalAmount / 100), 2, '.', ''));
        } else {
            throw new AliPayException('订单总金额不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param int $discountableAmount
     * @throws \Exception\Ali\AliPayException
     */
    public function setDiscountableAmount(int $discountableAmount){
        if(($discountableAmount > 0) && ($discountableAmount <= 10000000000)){
            $this->discountable_amount = $discountableAmount;
            $this->setBizContent('discountable_amount', number_format(($discountableAmount / 100), 2, '.', ''));
        } else {
            throw new AliPayException('可打折金额不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $body
     */
    public function setBody(string $body){
        $this->setBizContent('body', substr(trim($body), 0, 128));
    }

    /**
     * @param array $goodsDetail
     */
    public function setGoodsDetail(array $goodsDetail){
        if(!empty($goodsDetail)){
            $this->setBizContent('goods_detail', $goodsDetail);
        }
    }

    /**
     * @param string $operatorId
     */
    public function setOperatorId(string $operatorId){
        $this->setBizContent('operator_id', trim($operatorId));
    }

    /**
     * @param string $storeId
     */
    public function setStoreId(string $storeId){
        $this->setBizContent('store_id', trim($storeId));
    }

    /**
     * @param string $terminalId
     */
    public function setTerminalId(string $terminalId){
        $this->setBizContent('terminal_id', trim($terminalId));
    }

    /**
     * @param array $extendParams
     */
    public function setExtendParams(array $extendParams){
        if(!empty($extendParams)){
            $this->setBizContent('extend_params', $extendParams);
        }
    }

    /**
     * @param string $timeoutExpress
     */
    public function setTimeoutExpress(string $timeoutExpress){
        if(strlen($timeoutExpress) > 0){
            $this->setBizContent('timeout_express', $timeoutExpress);
        }
    }

    /**
     * @param string $authConfirmMode
     * @throws \Exception\Ali\AliPayException
     */
    public function setAuthConfirmMode(string $authConfirmMode){
        if(isset($this->authConfirmModeList[$authConfirmMode])){
            $this->setBizContent('auth_confirm_mode', $authConfirmMode);
        } else {
            throw new AliPayException('预授权确认模式不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param array $terminalParams
     */
    public function setTerminalParams(array $terminalParams){
        if(!empty($terminalParams)){
            $this->setBizContent('terminal_params', Tool::jsonEncode($terminalParams, JSON_UNESCAPED_UNICODE));
        }
    }

    public function getDetail() : array {
        if(!isset($this->biz_content['out_trade_no'])){
            throw new AliPayException('商户订单号不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if(!isset($this->biz_content['auth_code'])){
            throw new AliPayException('支付授权码不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if(!isset($this->biz_content['subject'])){
            throw new AliPayException('订单标题不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if(!isset($this->biz_content['total_amount'])){
            throw new AliPayException('订单总金额不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if($this->discountable_amount >= $this->total_amount){
            throw new AliPayException('可打折金额必须小于订单总金额', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        return $this->getContent();
    }
}