<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-5
 * Time: 上午12:37
 */
namespace DesignPatterns\Facades\PayApply;

use Constant\ErrorCode;
use Constant\ProjectCode;
use DesignPatterns\Facades\PayApplyFacade;
use Exception\Common\CheckException;
use Tool\SyUser;
use Tool\Tool;
use Traits\SimpleFacadeTrait;
use Wx\Shop\UnifiedOrder;
use Wx\WxUtilBase;
use Wx\WxUtilShop;

class WxMiniJs extends PayApplyFacade {
    use SimpleFacadeTrait;

    protected static function checkParams(array $data) : array {
        $wxOpenid = SyUser::getOpenId();
        if (strlen($wxOpenid) == 0) {
            throw new CheckException('请先微信登录', ProjectCode::USER_NOT_LOGIN_WX_AUTH);
        }

        return [
            'a00_openid' => $wxOpenid,
            'a00_appid' => Tool::getConfig('project.' . SY_ENV . SY_PROJECT . '.wx.appid.default'),
        ];
    }

    protected static function apply(array $data) : array {
        $order = new UnifiedOrder(UnifiedOrder::TRADE_TYPE_JSAPI, $data['a00_appid']);
        $order->setBody($data['content_result']['pay_name']);
        $order->setTotalFee($data['content_result']['pay_money']);
        $order->setOutTradeNo($data['content_result']['pay_sn']);
        $order->setAttach($data['content_result']['pay_attach']);
        $order->setOpenid($data['a00_openid']);
        $applyRes = WxUtilShop::applyJsPay($order, WxUtilBase::TYPE_MINI);
        unset($order);
        if($applyRes['code'] > 0){
            throw new CheckException($applyRes['message'], ErrorCode::COMMON_PARAM_ERROR);
        }

        return [
            'api' => $applyRes['data']['pay'],
        ];
    }
}