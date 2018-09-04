<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-04
 * Time: 1:47
 */
namespace Wx\Shop;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;
use Tool\Tool;
use Wx\WxUtilShop;

class PayMicro extends ShopBase {
    public function __construct(string $appId) {
        parent::__construct();
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->appid = $shopConfig->getAppId();
        $this->mch_id = $shopConfig->getPayMchId();
        $this->sign_type = 'MD5';
        $this->nonce_str = Tool::createNonceStr(32, 'numlower');
        $this->fee_type = 'CNY';
        $this->spbill_create_ip = $shopConfig->getClientIp();
    }

    private function __clone(){
    }

    /**
     * 公众号ID
     * @var string
     */
    private $appid = '';

    /**
     * 商户号
     * @var string
     */
    private $mch_id = '';

    /**
     * 设备号
     * @var string
     */
    private $device_info = '';

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
     * 商品描述
     * @var string
     */
    private $body = '';

    /**
     * 商品详情
     * @var string
     */
    private $detail = '';

    /**
     * 附加数据
     * @var string
     */
    private $attach = '';

    /**
     * 商户订单号
     * @var string
     */
    private $out_trade_no = '';

    /**
     * 订单金额
     * @var int
     */
    private $total_fee = 0;

    /**
     * 货币类型
     * @var string
     */
    private $fee_type = '';

    /**
     * 终端IP
     * @var string
     */
    private $spbill_create_ip = '';

    /**
     * 商品标记
     * @var string
     */
    private $goods_tag = '';

    /**
     * 授权码
     * @var string
     */
    private $auth_code = '';

    /**
     * @param string $body
     * @throws \Exception\Wx\WxException
     */
    public function setBody(string $body) {
        if (strlen($body) > 0) {
            $this->body = mb_substr($body, 0, 40);
        } else {
            throw new WxException('商品名称不能为空', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $attach
     * @throws \Exception\Wx\WxException
     */
    public function setAttach(string $attach) {
        if (strlen($attach) <= 127) {
            $this->attach = $attach;
        } else {
            throw new WxException('附加数据不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $outTradeNo
     * @throws \Exception\Wx\WxException
     */
    public function setOutTradeNo(string $outTradeNo) {
        if (preg_match('/^[0-9]{1,32}$/', $outTradeNo) > 0) {
            $this->out_trade_no = $outTradeNo;
        } else {
            throw new WxException('商户单号不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param int $totalFee
     * @throws \Exception\Wx\WxException
     */
    public function setTotalFee(int $totalFee) {
        if ($totalFee > 0) {
            $this->total_fee = $totalFee;
        } else {
            throw new WxException('支付金额不能小于0', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $authCode
     * @throws \Exception\Wx\WxException
     */
    public function setAuthCode(string $authCode) {
        if (preg_match('/^1[0-5][0-9]{16}$/', $authCode) > 0) {
            $this->auth_code = $authCode;
        } else {
            throw new WxException('授权码不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $device_info
     */
    public function setDeviceInfo(string $device_info) {
        $this->device_info = $device_info;
    }

    /**
     * @param string $detail
     */
    public function setDetail(string $detail) {
        $this->detail = $detail;
    }

    /**
     * @param string $goods_tag
     */
    public function setGoodsTag(string $goods_tag) {
        $this->goods_tag = $goods_tag;
    }

    public function getDetail() : array {
        if(strlen($this->body) == 0){
            throw new WxException('商品名称不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if(strlen($this->out_trade_no) == 0){
            throw new WxException('商户单号不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if($this->total_fee == 0){
            throw new WxException('支付金额必须大于0', ErrorCode::WX_PARAM_ERROR);
        }
        if(strlen($this->auth_code) == 0){
            throw new WxException('授权码不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        $resArr = [
            'appid' => $this->appid,
            'mch_id' => $this->mch_id,
            'sign_type' => $this->sign_type,
            'nonce_str' => $this->nonce_str,
            'fee_type' => $this->fee_type,
            'spbill_create_ip' => $this->spbill_create_ip,
            'body' => $this->body,
            'out_trade_no' => $this->out_trade_no,
            'total_fee' => $this->total_fee,
            'auth_code' => $this->auth_code,
        ];
        if(strlen($this->device_info) > 0){
            $resArr['device_info'] = $this->device_info;
        }
        if(strlen($this->detail) > 0){
            $resArr['detail'] = $this->detail;
        }
        if(strlen($this->attach) > 0){
            $resArr['attach'] = $this->attach;
        }
        if(strlen($this->goods_tag) > 0){
            $resArr['goods_tag'] = $this->goods_tag;
        }
        $resArr['sign'] = WxUtilShop::createSign($resArr, $this->appid);

        return $resArr;
    }
}