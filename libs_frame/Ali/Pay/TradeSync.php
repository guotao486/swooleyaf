<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-8
 * Time: 上午12:13
 */
namespace Ali\Pay;

use Ali\AliBase;
use Constant\ErrorCode;
use Exception\Ali\AliPayException;
use Tool\Tool;

class TradeSync extends AliBase {
    /**
     * 支付宝交易号
     * @var string
     */
    private $trade_no = '';
    /**
     * 商户订单号
     * @var string
     */
    private $out_request_no = '';
    /**
     * 业务类型
     * @var string
     */
    private $biz_type = '';
    /**
     * 同步信息
     * @var array
     */
    private $order_biz_info = [];

    public function __construct(string $appId){
        parent::__construct($appId);
        $this->setMethod('alipay.trade.orderinfo.sync');
    }

    public function __clone(){
    }

    /**
     * @param string $tradeNo
     * @throws \Exception\Ali\AliPayException
     */
    public function setTradeNo(string $tradeNo){
        if(ctype_digit($tradeNo)){
            $this->setBizContent('trade_no', $tradeNo);
        } else {
            throw new AliPayException('支付宝交易号不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $outRequestNo
     * @throws \Exception\Ali\AliPayException
     */
    public function setOutRequestNo(string $outRequestNo){
        if(ctype_digit($outRequestNo)){
            $this->setBizContent('out_request_no', $outRequestNo);
        } else {
            throw new AliPayException('商户订单号不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $bizType
     * @throws \Exception\Ali\AliPayException
     */
    public function setBizType(string $bizType){
        if(strlen($bizType) > 0){
            $this->setBizContent('biz_type', $bizType);
        } else {
            throw new AliPayException('业务类型不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param array $orderBizInfo
     * @throws \Exception\Ali\AliPayException
     */
    public function setOrderBizInfo(array $orderBizInfo){
        if(empty($orderBizInfo)){
            throw new AliPayException('同步信息不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        } else {
            $this->setBizContent('order_biz_info', Tool::jsonEncode($orderBizInfo, JSON_UNESCAPED_UNICODE));
        }
    }

    public function getDetail() : array {
        if(!isset($this->biz_content['out_request_no'])){
            throw new AliPayException('商户订单号不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if(!isset($this->biz_content['biz_type'])){
            throw new AliPayException('业务类型不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if(!isset($this->biz_content['order_biz_info'])){
            throw new AliPayException('同步信息不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        return $this->getContent();
    }
}