<?php
/**
 * 企业付款
 * User: jw
 * Date: 17-4-14
 * Time: 下午7:14
 */
namespace Wx\Shop;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;
use Tool\Tool;
use Wx\WxUtilShop;

class PayCompany extends ShopBase {
    private static $allowCheckOptions = [
        'NO_CHECK',
        'FORCE_CHECK',
    ];

    public function __construct(string $appId) {
        parent::__construct();
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->mch_appid = $shopConfig->getAppId();
        $this->mchid = $shopConfig->getPayMchId();
        $this->nonce_str = Tool::createNonceStr(32, 'numlower');
        $this->spbill_create_ip = $shopConfig->getClientIp();
    }

    private function __clone(){
    }

    /**
     * 公众账号ID
     * @var string
     */
    private $mch_appid = '';

    /**
     * 商户号
     * @var string
     */
    private $mchid = '';

    /**
     * 随机字符串
     * @var string
     */
    private $nonce_str = '';

    /**
     * 商户订单号
     * @var string
     */
    private $partner_trade_no = '';

    /**
     * 用户openid
     * @var string
     */
    private $openid = '';

    /**
     * 校验用户姓名选项
     * @var string
     */
    private $check_name = '';

    /**
     * 收款用户姓名
     * @var string
     */
    private $re_user_name = '';

    /**
     * 金额
     * @var int
     */
    private $amount = 0;

    /**
     * 企业付款描述信息
     * @var string
     */
    private $desc = '';

    /**
     * Ip地址
     * @var string
     */
    private $spbill_create_ip = '';

    /**
     * @param string $outTradeNo
     * @throws \Exception\Wx\WxException
     */
    public function setOutTradeNo(string $outTradeNo) {
        if (preg_match('/^[0-9]{1,32}$/', $outTradeNo) > 0) {
            $this->partner_trade_no = $outTradeNo;
        } else {
            throw new WxException('商户单号不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $openid
     * @throws \Exception\Wx\WxException
     */
    public function setOpenid(string $openid) {
        if (preg_match('/^[0-9a-zA-Z\-\_]{28}$/', $openid) > 0) {
            $this->openid = $openid;
        } else {
            throw new WxException('用户openid不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $checkName
     * @throws \Exception\Wx\WxException
     */
    public function setCheckName(string $checkName) {
        if (in_array($checkName, self::$allowCheckOptions)) {
            $this->check_name = $checkName;
        } else {
            throw new WxException('校验用户姓名选项不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $userName
     */
    public function setReUserName(string $userName) {
        $this->re_user_name = $userName;
    }

    /**
     * @param int $amount
     * @throws \Exception\Wx\WxException
     */
    public function setAmount(int $amount) {
        $this->amount = $amount;
        if ($amount > 0) {
            $this->amount = $amount;
        } else {
            throw new WxException('付款金额必须大于0', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $desc
     * @throws \Exception\Wx\WxException
     */
    public function setDesc(string $desc) {
        if (strlen($desc) > 0) {
            $this->desc = $desc;
        } else {
            throw new WxException('付款描述信息不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(strlen($this->partner_trade_no) == 0){
            throw new WxException('商户单号不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if(strlen($this->openid) == 0){
            throw new WxException('用户openid不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if(strlen($this->check_name) == 0){
            throw new WxException('校验用户姓名选项不能为空', ErrorCode::WX_PARAM_ERROR);
        } else if(($this->check_name == 'FORCE_CHECK') && (strlen($this->re_user_name) == 0)){
            throw new WxException('收款用户姓名不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if(strlen($this->desc) == 0){
            throw new WxException('付款描述信息不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if($this->amount <= 0){
            throw new WxException('付款金额必须大于0', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr = [
            'mch_appid' => $this->mch_appid,
            'mchid' => $this->mchid,
            'nonce_str' => $this->nonce_str,
            'spbill_create_ip' => $this->spbill_create_ip,
            'partner_trade_no' => $this->partner_trade_no,
            'openid' => $this->openid,
            'check_name' => $this->check_name,
            'amount' => $this->amount,
            'desc' => $this->desc,
        ];
        if(strlen($this->re_user_name) > 0){
            $resArr['re_user_name'] = $this->re_user_name;
        }
        $resArr['sign'] = WxUtilShop::createSign($resArr, $this->mch_appid);

        return $resArr;
    }
}