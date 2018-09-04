<?php
/**
 * 企业付款查询
 * User: jw
 * Date: 17-4-14
 * Time: 下午11:30
 */
namespace Wx\Shop;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;
use Tool\Tool;
use Wx\WxUtilShop;

class PayCompanyQuery extends ShopBase {
    public function __construct(string $appId) {
        parent::__construct();
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->appid = $shopConfig->getAppId();
        $this->mch_id = $shopConfig->getPayMchId();
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
     * 商户号
     * @var string
     */
    private $mch_id = '';

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

    public function getDetail() : array {
        if(strlen($this->partner_trade_no) == 0){
            throw new WxException('商户单号不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr = [
            'appid' => $this->appid,
            'mch_id' => $this->mch_id,
            'nonce_str' => $this->nonce_str,
            'partner_trade_no' => $this->partner_trade_no,
        ];
        $resArr['sign'] = WxUtilShop::createSign($resArr, $this->appid);

        return $resArr;
    }
}