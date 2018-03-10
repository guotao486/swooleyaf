<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/3/2 0002
 * Time: 11:30
 */
namespace Constant;

use Traits\SimpleTrait;

final class Project {
    use SimpleTrait;

    //公共常量
    const COMMON_PAGE_DEFAULT = 1; //默认页数
    const COMMON_LIMIT_DEFAULT = 10; //默认分页限制
    const COMMON_DBNAME_DEFAULT = 'sytrain'; //默认数据库名

    //订单常量
    const ORDER_PAY_TYPE_GOODS = '1000'; //支付类型-商品
    const ORDER_REFUND_TYPE_GOODS = '5000'; //退款类型-商品

    //支付常量
    const PAY_TYPE_WX = 1; //类型-微信支付
    const PAY_TYPE_ALI = 2; //类型-支付宝支付
    const PAY_MODEL_TYPE_WX_JS = 'a00'; //模式类型-微信js支付
    const PAY_MODEL_TYPE_WX_NATIVE_DYNAMIC = 'a01'; //模式类型-微信动态扫码支付
    const PAY_MODEL_TYPE_WX_NATIVE_STATIC = 'a02'; //模式类型-微信静态扫码支付
    const PAY_MODEL_TYPE_ALI_WEB = 'b00'; //模式类型-支付宝网页支付
    const PAY_MODEL_TYPE_ALI_CODE = 'b01'; //模式类型-支付宝扫码支付
}
