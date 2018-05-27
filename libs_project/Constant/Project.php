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

    //模块常量
    public static $totalModuleName = [
        self::MODULE_NAME_API,
    ];
    public static $totalModuleBase = [
        self::MODULE_BASE_API,
    ];
    const MODULE_BASE_API = 'api';
    const MODULE_NAME_API = SY_PROJECT . self::MODULE_BASE_API;

    //公共常量
    const COMMON_PAGE_DEFAULT = 1; //默认页数
    const COMMON_LIMIT_DEFAULT = 10; //默认分页限制

    //任务常量
    const TASK_STATUS_DELETE = -1; //任务状态-已删除
    const TASK_STATUS_INVALID = 0; //任务状态-无效
    const TASK_STATUS_VALID = 1; //任务状态-有效
    const TASK_PERSIST_TYPE_SINGLE = 1; //持久化类型-单次
    const TASK_PERSIST_TYPE_INTERVAL = 2; //持久化类型-间隔时间
    const TASK_PERSIST_TYPE_CRON = 3; //持久化类型-cron计划

    //REDIS常量 以sy000开头的前缀为框架内部前缀,以sy+3位数字开头的前缀为公共模块前缀
    const REDIS_PREFIX_SESSION = 'sy000001_'; //前缀-session
    const REDIS_PREFIX_ORDER_SN = 'sy001000_'; //前缀-订单单号
    const REDIS_PREFIX_MESSAGE_QUEUE = 'sy001001_'; //前缀-消息队列
    const REDIS_PREFIX_WX_ACCOUNT = 'sy002000_'; //前缀-微信公众号
    const REDIS_PREFIX_WX_COMPONENT_ACCOUNT = 'sy002001_'; //前缀-微信开放平台账号
    const REDIS_PREFIX_WX_COMPONENT_AUTHORIZER = 'sy002002_'; //前缀-微信开放平台授权公众号

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

    //消息队列常量
    const MESSAGE_QUEUE_TOPIC_REDIS_ADD_LOG = 'a000'; //redis主题-添加日志
    const MESSAGE_QUEUE_TOPIC_REDIS_REQ_HEALTH_CHECK = 'a001'; //redis主题-请求健康检查
    const MESSAGE_QUEUE_TOPIC_KAFKA_ADD_MYSQL_LOG = 'b000'; //kafka主题-添加mysql日志

    //时间常量
    const TIME_EXPIRE_LOCAL_USER_CACHE = 300; //超时时间-本地用户缓存,单位为秒
    const TIME_EXPIRE_LOCAL_API_SIGN_CACHE = 300; //超时时间-本地api签名缓存,单位为秒
    const TIME_EXPIRE_LOCAL_WXSHOP_REFRESH = 600; //超时时间-本地微信商户号更新,单位为秒
    const TIME_EXPIRE_LOCAL_WXSHOP_CLEAR = 3600; //超时时间-本地微信商户号清理,单位为秒
    const TIME_EXPIRE_LOCAL_WXSHOP_TOKEN_CLEAR = 300; //超时时间-本地微信商户号token清理,单位为秒
    const TIME_EXPIRE_LOCAL_WXOPEN_AUTHORIZER_TOKEN_CLEAR = 300; //超时时间-本地微信开放平台授权者token清理,单位为秒
    const TIME_EXPIRE_LOCAL_ALIPAY_REFRESH = 600; //超时时间-本地支付宝支付更新,单位为秒
    const TIME_EXPIRE_LOCAL_ALIPAY_CLEAR = 3600; //超时时间-本地支付宝支付清理,单位为秒
    const TIME_EXPIRE_SESSION = 259200; //超时时间-session,单位为秒
}
