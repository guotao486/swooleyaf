<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/11 0011
 * Time: 11:21
 */
namespace Wx2\Shop;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;
use Tool\Tool;
use Wx2\WxBaseShop;
use Wx2\WxUtilBase;
use Wx2\WxUtilOpenBase;
use Wx2\WxUtilShop;

class JsConfig extends WxBaseShop {
    /**
     * @var int
     */
    private $timestamp = 0;
    /**
     * @var string
     */
    private $nonceStr = '';
    /**
     * 平台类型 shop：公众号 openshop：第三方平台代理公众号
     * @var string
     */
    private $platType = '';

    public function __construct(string $appId) {
        parent::__construct();
        $this->reqData['appId'] = $appId;
        $this->reqData['timestamp'] = Tool::getNowTime();
        $this->reqData['nonceStr'] = Tool::createNonceStr(32, 'numlower');
        $this->platType = WxUtilBase::TYPE_SHOP;
    }

    private function __clone(){
    }

    /**
     * @param string $platType
     * @throws \Exception\Wx\WxException
     */
    public function setPlatType(string $platType){
        if(in_array($platType, [WxUtilBase::TYPE_SHOP, WxUtilBase::TYPE_OPEN_SHOP])){
            $this->platType = $platType;
        } else {
            throw new WxException('平台类型不支持', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $platType 平台类型 shop：公众号 openshop：第三方平台代理公众号
     * @return array
     */
    public function getDetail() : array {
        if ($this->platType == WxUtilBase::TYPE_SHOP) { //公众号获取jsapi_ticket
            $ticket = WxUtilShop::getJsTicket($this->reqData['appId']);
        } else { //第三方平台获取jsapi_ticket
            $ticket = WxUtilOpenBase::getAuthorizerJsTicket($this->reqData['appId']);
        }

        $needStr = 'jsapi_ticket=' . $ticket . '&noncestr=' . $this->reqData['nonceStr'] . '&timestamp=' . $this->reqData['nonceStr'] . '&url=' . WxConfigSingleton::getInstance()->getShopConfig($this->reqData['appId'])->getPayAuthUrl();
        $this->reqData['signature'] = sha1($needStr);
        return $this->reqData;
    }
}