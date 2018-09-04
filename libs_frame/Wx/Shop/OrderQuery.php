<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-03
 * Time: 22:34
 */
namespace Wx\Shop;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;
use Tool\Tool;
use Wx\WxUtilShop;

class OrderQuery extends ShopBase {
    public function __construct(string $appId) {
        parent::__construct();
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->appid = $shopConfig->getAppId();
        $this->mch_id = $shopConfig->getPayMchId();
        $this->sign_type = 'MD5';
        $this->nonce_str = Tool::createNonceStr(32, 'numlower');
    }

    private function __clone(){
    }

    /**
     * 公众账号ID
     * @var string
     */
    private $appid = '';

    /**
     * 商户号
     * @var string
     */
    private $mch_id = '';

    /**
     * 微信订单号
     * @var string
     */
    private $transaction_id = '';

    /**
     * 商户订单号
     * @var string
     */
    private $out_trade_no = '';

    /**
     * 随机字符串
     * @var string
     */
    private $nonce_str = '';

    /**
     * 签名类型
     * @var string
     */
    private $sign_type = '';

    /**
     * @param string $transactionId
     * @throws \Exception\Wx\WxException
     */
    public function setTransactionId(string $transactionId) {
        if (preg_match('/^4[0-9]{27}$/', $transactionId) > 0) {
            $this->transaction_id = $transactionId;
        } else {
            throw new WxException('微信订单号不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $outTradeNo
     * @throws \Exception\Wx\WxException
     */
    public function setOutTradeNo(string $outTradeNo) {
        if (preg_match('/^[a-zA-Z0-9]{1,32}$/', $outTradeNo) > 0) {
            $this->out_trade_no = $outTradeNo . '';
        } else {
            throw new WxException('商户订单号不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if((strlen($this->transaction_id) == 0) && (strlen($this->out_trade_no) == 0)){
            throw new WxException('微信订单号与商户订单号不能同时为空', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr = [
            'appid' => $this->appid,
            'mch_id' => $this->mch_id,
            'sign_type' => $this->sign_type,
            'nonce_str' => $this->nonce_str,
        ];
        if(strlen($this->transaction_id) > 0){
            $resArr['transaction_id'] = $this->transaction_id;
        } else {
            $resArr['out_trade_no'] = $this->out_trade_no;
        }
        $resArr['sign'] = WxUtilShop::createSign($resArr, $this->appid);

        return $resArr;
    }
}