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

    //REDIS常量 以sy000开头的前缀为框架内部前缀,以sy+3位数字开头的前缀为公共模块前缀
    const REDIS_PREFIX_SESSION = 'sy000001_'; //前缀-session
    const REDIS_PREFIX_CODE_IMAGE = 'sy001000_'; //前缀-验证码图片
    const REDIS_PREFIX_ORDER_SN = 'sy001001_'; //前缀-订单单号
    const REDIS_PREFIX_MESSAGE_QUEUE = 'sy001002_'; //前缀-消息队列
    const REDIS_PREFIX_IMAGE_DATA = 'sy001003_'; //前缀-图片缓存
    const REDIS_PREFIX_WX_ACCOUNT = 'sy002000_'; //前缀-微信公众号
    const REDIS_PREFIX_WX_COMPONENT_ACCOUNT = 'sy002001_'; //前缀-微信开放平台账号
    const REDIS_PREFIX_WX_COMPONENT_AUTHORIZER = 'sy002002_'; //前缀-微信开放平台授权公众号
    const REDIS_PREFIX_WX_NATIVE_PRE = 'sy002003_'; //前缀-微信扫码预支付

    //消息队列常量
    const MESSAGE_QUEUE_TOPIC_REDIS_ADD_LOG = 'a000'; //redis主题-添加日志
    const MESSAGE_QUEUE_TOPIC_REDIS_REQ_HEALTH_CHECK = 'a001'; //redis主题-请求健康检查
    const MESSAGE_QUEUE_TOPIC_KAFKA_ADD_MYSQL_LOG = 'b000'; //kafka主题-添加mysql日志

    //支付常量
    const PAY_WAY_WX = 1; //方式-微信
    const PAY_WAY_ALI = 2; //方式-支付宝
    const PAY_TYPE_WX_JS = 'a000'; //类型-微信js支付
    const PAY_TYPE_WX_NATIVE_DYNAMIC = 'a001'; //类型-微信动态扫码支付
    const PAY_TYPE_WX_NATIVE_STATIC = 'a002'; //类型-微信静态扫码支付
    const PAY_TYPE_ALI_CODE = 'a100'; //类型-支付宝扫码支付
    const PAY_TYPE_ALI_WEB = 'a101'; //类型-支付宝网页支付

    //微信开放平台常量
    const WX_COMPONENT_AUTHORIZER_STATUS_CANCEL = 0; //授权公众号状态-取消授权
    const WX_COMPONENT_AUTHORIZER_STATUS_ALLOW = 1; //授权公众号状态-允许授权
    const WX_COMPONENT_AUTHORIZER_EXPIRE_TOKEN = 7000; //授权公众号token超时时间,单位为秒
    const WX_COMPONENT_AUTHORIZER_OPTION_TYPE_AUTHORIZED = 1; //授权公众号操作类型-允许授权
    const WX_COMPONENT_AUTHORIZER_OPTION_TYPE_UNAUTHORIZED = 2; //授权公众号操作类型-取消授权
    const WX_COMPONENT_AUTHORIZER_OPTION_TYPE_AUTHORIZED_UPDATE = 3; //授权公众号操作类型-更新授权

    //微信商户号常量
    const WX_SHOP_STATUS_DISABLE = 0; //商户号状态-无效
    const WX_SHOP_STATUS_ENABLE = 1; //商户号状态-有效
    const WX_SHOP_EXPIRE_TOKEN = 7000; //商户号token超时时间,单位为秒

    //支付宝支付常量
    const ALI_PAY_STATUS_DISABLE = 0; //状态-无效
    const ALI_PAY_STATUS_ENABLE = 1; //状态-有效

    //订单常量
    const ORDER_PAY_TYPE_GOODS = '1000'; //支付类型-商品
    const ORDER_REFUND_TYPE_GOODS = '5000'; //退款类型-商品
}
