<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-11
 * Time: 下午11:49
 */
namespace Wx2\Shop;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;
use Tool\Tool;
use Wx2\WxBaseShop;
use Wx2\WxUtilBase;
use Wx2\WxUtilShop;

class PayMicro extends WxBaseShop {
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

    public function __construct(string $appId){
        parent::__construct();
        $this->serviceUrl = 'https://api.mch.weixin.qq.com/pay/micropay';
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->reqData['appid'] = $shopConfig->getAppId();
        $this->reqData['mch_id'] = $shopConfig->getPayMchId();
        $this->reqData['spbill_create_ip'] = $shopConfig->getClientIp();
        $this->reqData['sign_type'] = 'MD5';
        $this->reqData['nonce_str'] = Tool::createNonceStr(32, 'numlower');
        $this->reqData['fee_type'] = 'CNY';
        $this->reqData['total_fee'] = 0;
    }

    public function __clone(){
    }

    /**
     * @param string $body
     * @throws \Exception\Wx\WxException
     */
    public function setBody(string $body) {
        if (strlen($body) > 0) {
            $this->reqData['body'] = mb_substr($body, 0, 40);
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
            $this->reqData['attach'] = $attach;
        } else {
            throw new WxException('附加数据不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $outTradeNo
     * @throws \Exception\Wx\WxException
     */
    public function setOutTradeNo(string $outTradeNo) {
        if(ctype_digit($outTradeNo) && (strlen($outTradeNo) <= 32)){
            $this->reqData['out_trade_no'] = $outTradeNo;
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
            $this->reqData['total_fee'] = $totalFee;
        } else {
            throw new WxException('支付金额不能小于0', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $authCode
     * @throws \Exception\Wx\WxException
     */
    public function setAuthCode(string $authCode) {
        if(ctype_digit($authCode) && (strlen($authCode) == 18) && ($authCode{0} == '1')){
            $this->reqData['auth_code'] = $authCode;
        } else {
            throw new WxException('授权码不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $deviceInfo
     */
    public function setDeviceInfo(string $deviceInfo) {
        if(strlen($deviceInfo) > 0){
            $this->reqData['device_info'] = $deviceInfo;
        }
    }

    /**
     * @param string $detail
     */
    public function setDetail(string $detail) {
        if(strlen($detail) > 0){
            $this->reqData['detail'] = $detail;
        }
    }

    /**
     * @param string $goodsTag
     */
    public function setGoodsTag(string $goodsTag) {
        if(strlen($goodsTag) > 0){
            $this->reqData['goods_tag'] = $goodsTag;
        }
    }

    public function getDetail() : array {
        if(!isset($this->reqData['body'])){
            throw new WxException('商品名称不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if(!isset($this->reqData['out_trade_no'])){
            throw new WxException('商户单号不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if($this->reqData['total_fee'] <= 0){
            throw new WxException('支付金额必须大于0', ErrorCode::WX_PARAM_ERROR);
        }
        if(!isset($this->reqData['auth_code'])){
            throw new WxException('授权码不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        $this->reqData['sign'] = WxUtilShop::createSign($this->reqData, $this->reqData['appid']);

        $resArr = [
            'code' => 0
        ];

        $this->curlConfigs[CURLOPT_URL] = $this->serviceUrl;
        $this->curlConfigs[CURLOPT_POSTFIELDS] = Tool::arrayToXml($this->reqData);
        $sendRes = WxUtilBase::sendPostReq($this->curlConfigs);
        $sendData = Tool::xmlToArray($sendRes);
        if ($sendData['return_code'] == 'FAIL') {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $sendData['return_msg'];
        } else if ($sendData['result_code'] == 'FAIL') {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $sendData['err_code_des'];
        } else {
            $resArr['data'] = $sendData;
        }

        return $resArr;
    }
}