<?php
/**
 * 扫码支付模式一预支付类
 * User: jw
 * Date: 17-4-2
 * Time: 上午10:13
 */
namespace Wx\Shop;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;
use Tool\Tool;
use Wx\WxUtilShop;

class PayNativePre extends ShopBase {
    public function __construct(string $appId) {
        parent::__construct();
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->appid = $shopConfig->getAppId();
        $this->mch_id = $shopConfig->getPayMchId();
        $this->time_stamp = Tool::getNowTime();
        $this->nonce_str = Tool::createNonceStr(32);
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
     * 当前时间戳
     * @var int
     */
    private $time_stamp = 0;

    /**
     * 随机字符串，不长于32位
     * @var string
     */
    private $nonce_str = '';

    /**
     * 商户定义的商品id
     * @var string
     */
    private $product_id = '';

    /**
     * @param string $productId
     * @throws \Exception\Wx\WxException
     */
    public function setProductId(string $productId) {
        if (preg_match('/^[a-zA-Z0-9]{1,32}$/', $productId) > 0) {
            $this->product_id = $productId;
        } else {
            throw  new WxException('商品ID不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * 获取预支付订单详情
     * @return array
     * @throws \Exception\Wx\WxException
     */
    public function getDetail() : array {
        if(strlen($this->product_id) == 0){
            throw  new WxException('商品ID不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr = [
            'appid' => $this->appid,
            'mch_id' => $this->mch_id,
            'time_stamp' => $this->time_stamp,
            'nonce_str' => $this->nonce_str,
            'product_id' => $this->product_id,
        ];
        $resArr['sign'] = WxUtilShop::createSign($resArr, $this->appid);

        return $resArr;
    }
}