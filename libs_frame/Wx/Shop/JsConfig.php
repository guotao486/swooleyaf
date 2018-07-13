<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-1
 * Time: 上午7:39
 */
namespace Wx\Shop;

use DesignPatterns\Singletons\WxConfigSingleton;
use Tool\Tool;
use Wx\WxUtilOpen;
use Wx\WxUtilShop;

class JsConfig {
    public function __construct(string $appId) {
        $this->appId = $appId;
        $this->timestamp = Tool::getNowTime();
        $this->nonceStr = WxUtilShop::createNonceStr();
    }

    /**
     * @var string
     */
    private $appId = '';

    /**
     * @var int
     */
    private $timestamp = 0;

    /**
     * @var string
     */
    private $nonceStr = '';

    /**
     * @param string $platType 平台类型 shop：公众号 open：第三方平台
     * @return array
     */
    public function getDetail(string $platType='shop') : array {
        $resArr = [
            'appId' => $this->appId,
            'timestamp' => $this->timestamp,
            'nonceStr' => $this->nonceStr,
        ];

        if ($platType == 'shop') { //公众号获取jsapi_ticket
            $ticket = WxUtilShop::getJsTicket($this->appId);
        } else { //第三方平台获取jsapi_ticket
            $ticket = WxUtilOpen::getAuthorizerJsTicket($this->appId);
        }

        $needStr = 'jsapi_ticket=' . $ticket . '&noncestr=' . $this->nonceStr . '&timestamp=' . $this->timestamp . '&url=' . WxConfigSingleton::getInstance()->getShopConfig($this->appId)->getPayAuthUrl();
        $resArr['signature'] = sha1($needStr);

        return $resArr;
    }
}