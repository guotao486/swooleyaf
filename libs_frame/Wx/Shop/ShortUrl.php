<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-2
 * Time: 上午11:00
 */
namespace Wx\Shop;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;
use Tool\Tool;
use Wx\WxUtilShop;

class ShortUrl extends ShopBase {
    public function __construct(string $appId) {
        parent::__construct();
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->appid = $shopConfig->getAppId();
        $this->mch_id = $shopConfig->getPayMchId();
        $this->nonce_str = Tool::createNonceStr(32);
        $this->sign_type = 'MD5';
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
     * URL链接
     * @var string
     */
    private $long_url = '';

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
     * @param string $longUrl
     * @throws \Exception\Wx\WxException
     */
    public function setLongUrl(string $longUrl) {
        if (preg_match('/^weixin/', $longUrl) > 0) {
            $this->long_url = $longUrl;
        } else {
            throw new WxException('长链接不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(strlen($this->long_url) == 0){
            throw  new WxException('长链接不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr = [
            'appid' => $this->appid,
            'mch_id' => $this->mch_id,
            'nonce_str' => $this->nonce_str,
            'sign_type' => $this->sign_type,
            'long_url' => $this->long_url,
        ];
        $resArr['sign'] = WxUtilShop::createSign($resArr, $this->appid);

        return $resArr;
    }
}