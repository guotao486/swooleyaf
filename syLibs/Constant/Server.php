<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/3/12 0012
 * Time: 9:30
 */
namespace Constant;

use Traits\SimpleTrait;

final class Server {
    use SimpleTrait;

    public static $totalModules = [
        self::MODULE_NAME_API,
        self::MODULE_NAME_ORDER,
        self::MODULE_NAME_USER,
        self::MODULE_NAME_SERVICE,
    ];

    public static $totalModuleBases = [
        self::MODULE_BASE_API,
        self::MODULE_BASE_ORDER,
        self::MODULE_BASE_USER,
        self::MODULE_BASE_SERVICE,
    ];

    //模块常量
    //考虑到yac缓存名称长度限制,名称不能超过30个字符串
    const MODULE_BASE_API = 'api';
    const MODULE_BASE_ORDER = 'order';
    const MODULE_BASE_USER = 'user';
    const MODULE_BASE_SERVICE = 'services';
    const MODULE_NAME_API = SY_PROJECT . self::MODULE_BASE_API;
    const MODULE_NAME_ORDER = SY_PROJECT . self::MODULE_BASE_ORDER;
    const MODULE_NAME_USER = SY_PROJECT . self::MODULE_BASE_USER;
    const MODULE_NAME_SERVICE = SY_PROJECT . self::MODULE_BASE_SERVICE;

    //服务常量
    const SERVER_PACKAGE_MAX_LENGTH = 12582912; //服务端消息最大长度-12M
    const SERVER_OUTPUT_MAX_LENGTH = 5242880; //服务端输出内容最大长度-5M
    const SERVER_TYPE_API = 'api'; //服务端类型-api
    const SERVER_TYPE_RPC = 'rpc'; //服务端类型-rpc
    const SERVER_HTTP_TAG_RESPONSE_EOF = "\r\r\rswoole@yaf\r\r\r"; //服务端http标识-响应结束符
    const SERVER_HTTP_TAG_REQUEST_HEADER = 'swoole-yaf'; //服务端http标识-请求头名称
    const SERVER_DATA_KEY_TASK = '_sytask'; //服务端内部数据键名-task
    const SERVER_DATA_KEY_TOKEN = '_sytoken'; //服务端内部数据键名-token
    const SERVER_TIME_REQ_HANDLE_MAX = 120; //服务端时间-请求最大执行时间,单位为毫秒
    const SERVER_TIME_REQ_HEALTH_MIN = 4000; //服务端时间-请求健康检查最小时间,单位为毫秒

    //REDIS常量 以sy000开头的前缀为框架内部前缀,以sy+3位数字开头的前缀为公共模块前缀
    const REDIS_PREFIX_SESSION = 'sy000001_'; //前缀-session
    const REDIS_PREFIX_TIMER = 'sy001001_'; //前缀-定时器
    const REDIS_PREFIX_CODE_IMAGE = 'sy001002_'; //前缀-验证码图片
    const REDIS_PREFIX_ORDER_SN = 'sy001003_'; //前缀-订单单号
    const REDIS_PREFIX_REQUEST_SIGN = 'sy001004_'; //前缀-请求签名
    const REDIS_PREFIX_MESSAGE_QUEUE = 'sy001005_'; //前缀-消息队列
    const REDIS_PREFIX_IMAGE_DATA = 'sy001006_'; //前缀-图片缓存
    const REDIS_PREFIX_WX_ACCOUNT = 'sy002000_'; //前缀-微信公众号
    const REDIS_PREFIX_WX_COMPONENT_ACCOUNT = 'sy002001_'; //前缀-微信开放平台账号
    const REDIS_PREFIX_WX_COMPONENT_AUTHORIZER = 'sy002002_'; //前缀-微信开放平台授权公众号
    const REDIS_PREFIX_WX_NATIVE_PRE = 'sy002003_'; //前缀-微信扫码预支付

    //微信开放平台常量
    const WX_COMPONENT_AUTHORIZER_STATUS_CANCEL = 0; //授权公众号状态-取消授权
    const WX_COMPONENT_AUTHORIZER_STATUS_ALLOW = 1; //授权公众号状态-允许授权
    const WX_COMPONENT_AUTHORIZER_EXPIRE_ACCESS_TOKEN = 7000; //授权公众号超时时间-access token,单位为秒
    const WX_COMPONENT_AUTHORIZER_EXPIRE_JS_TICKET = 7000; //授权公众号超时时间-js ticket,单位为秒

    //微信商户号常量
    const WX_SHOP_STATUS_DISABLE = 0; //商户号状态-无效
    const WX_SHOP_STATUS_ENABLE = 1; //商户号状态-有效
    const WX_SHOP_EXPIRE_ACCESS_TOKEN = 7000; //商户号超时时间-access token,单位为秒
    const WX_SHOP_EXPIRE_JS_TICKET = 7000; //商户号超时时间-js ticket,单位为秒

    //本地缓存标识
    const CACHE_LOCAL_TAG_PREFIX_WX_COMPONENT_ACCESS_TOKEN = 'a101_'; //前缀-微信开放平台access token

    //校验器常量
    const VALIDATOR_STRING_TYPE_REQUIRED = 'string_required'; //字符串校验器类型-必填
    const VALIDATOR_STRING_TYPE_MIN = 'string_min'; //字符串校验器类型-最小长度
    const VALIDATOR_STRING_TYPE_MAX = 'string_max'; //字符串校验器类型-最大长度
    const VALIDATOR_STRING_TYPE_REGEX = 'string_regex'; //字符串校验器类型-正则表达式
    const VALIDATOR_STRING_TYPE_PHONE = 'string_phone'; //字符串校验器类型-手机号码
    const VALIDATOR_STRING_TYPE_TEL = 'string_tel'; //字符串校验器类型-联系方式
    const VALIDATOR_STRING_TYPE_EMAIL = 'string_email'; //字符串校验器类型-邮箱
    const VALIDATOR_STRING_TYPE_URL = 'string_url'; //字符串校验器类型-URL链接
    const VALIDATOR_STRING_TYPE_JSON = 'string_json'; //字符串校验器类型-JSON
    const VALIDATOR_STRING_TYPE_SIGN = 'string_sign'; //字符串校验器类型-请求签名
    const VALIDATOR_STRING_TYPE_BASE_IMAGE = 'string_baseimage'; //字符串校验器类型-base64编码图片
    const VALIDATOR_STRING_TYPE_IP = 'string_ip'; //字符串校验器类型-IP
    const VALIDATOR_STRING_TYPE_LNG = 'string_lng'; //字符串校验器类型-经度
    const VALIDATOR_STRING_TYPE_LAT = 'string_lat'; //字符串校验器类型-纬度
    const VALIDATOR_STRING_TYPE_NO_JS = 'string_nojs'; //字符串校验器类型-不允许js脚本
    const VALIDATOR_STRING_TYPE_NO_EMOJI = 'string_noemoji'; //字符串校验器类型-不允许emoji表情
    const VALIDATOR_STRING_TYPE_ZH = 'string_zh'; //字符串校验器类型-中文,数字,字母
    const VALIDATOR_INT_TYPE_REQUIRED = 'int_required'; //整数校验器类型-必填
    const VALIDATOR_INT_TYPE_MIN = 'int_min'; //整数校验器类型-最小值
    const VALIDATOR_INT_TYPE_MAX = 'int_max'; //整数校验器类型-最大值
    const VALIDATOR_INT_TYPE_IN = 'int_in'; //整数校验器类型-取值枚举
    const VALIDATOR_INT_TYPE_BETWEEN = 'int_between'; //整数校验器类型-取值区间
    const VALIDATOR_DOUBLE_TYPE_REQUIRED = 'double_required'; //浮点数校验器类型-必填
    const VALIDATOR_DOUBLE_TYPE_MIN = 'double_min'; //浮点数校验器类型-最小值
    const VALIDATOR_DOUBLE_TYPE_MAX = 'double_max'; //浮点数校验器类型-最大值
    const VALIDATOR_DOUBLE_TYPE_BETWEEN = 'double_between'; //浮点数校验器类型-取值区间

    //路由常量
    const ROUTE_TYPE_BASIC = 'basic'; //类型-基础路由

    //注册常量
    const REGISTRY_NAME_SERVICE_ERROR = 'SERVICE_ERROR'; //名称-服务错误
    const REGISTRY_NAME_REQUEST_HEADER = 'REQUEST_HEADER'; //名称-请求头
    const REGISTRY_NAME_REQUEST_SERVER = 'REQUEST_SERVER'; //名称-服务器信息
    const REGISTRY_NAME_RESPONSE_HEADER = 'RESPONSE_HEADER'; //名称-响应头
    const REGISTRY_NAME_RESPONSE_COOKIE = 'RESPONSE_COOKIE'; //名称-响应cookie

    //图片常量
    const IMAGE_MIME_TYPE_PNG = 'image/png'; //MIME类型-PNG
    const IMAGE_MIME_TYPE_JPEG = 'image/jpeg'; //MIME类型-JPEG
    const IMAGE_MIME_TYPE_GIF = 'image/gif'; //MIME类型-GIF

    //消息队列常量
    const MESSAGE_QUEUE_TOPIC_REDIS_ADD_LOG = 'a000'; //redis主题-添加日志
    const MESSAGE_QUEUE_TOPIC_REDIS_REQ_HEALTH_CHECK = 'a001'; //redis主题-请求健康检查
    const MESSAGE_QUEUE_TOPIC_KAFKA_ADD_MYSQL_LOG = 'b000'; //kafka主题-添加mysql日志

    //任务常量,4位字符串,数字和字母组成,纯数字的为框架内部任务,其他为自定义任务
    const TASK_TYPE_CLEAR_API_SIGN_CACHE = '0001'; //任务类型-清理api签名缓存
    const TASK_TYPE_CLEAR_LOCAL_USER_CACHE = '0002'; //任务类型-清除本地用户信息缓存
    const TASK_TYPE_CLEAR_LOCAL_WXSHOP_TOKEN_CACHE = '0003'; //任务类型-清除本地微信商户号token缓存
    const TASK_TYPE_CLEAR_LOCAL_WXOPEN_AUTHORIZER_TOKEN_CACHE = '0004'; //任务类型-清除本地微信开放平台授权者token缓存

    //支付常量
    const PAY_WAY_WX = 1; //方式-微信
    const PAY_WAY_ALI = 2; //方式-支付宝
    const PAY_TYPE_WX_JS = 'a000'; //类型-微信js支付
    const PAY_TYPE_WX_NATIVE_DYNAMIC = 'a001'; //类型-微信动态扫码支付
    const PAY_TYPE_WX_NATIVE_STATIC = 'a002'; //类型-微信静态扫码支付
    const PAY_TYPE_ALI_CODE = 'a100'; //类型-支付宝扫码支付
    const PAY_TYPE_ALI_WEB = 'a101'; //类型-支付宝网页支付

    //时间常量
    const TIME_EXPIRE_LOCAL_USER_CACHE = 300; //超时时间-本地用户缓存,单位为秒
    const TIME_EXPIRE_LOCAL_API_SIGN_CACHE = 300; //超时时间-本地api签名缓存,单位为秒
    const TIME_EXPIRE_LOCAL_WXSHOP_CACHE = 600; //超时时间-本地微信商户号缓存,单位为秒
    const TIME_EXPIRE_LOCAL_WXSHOP_CLEAR = 3600; //超时时间-本地微信商户号清理,单位为秒
    const TIME_EXPIRE_LOCAL_WXSHOP_TOKEN_CLEAR = 300; //超时时间-本地微信商户号token清理,单位为秒
    const TIME_EXPIRE_LOCAL_WXOPEN_AUTHORIZER_TOKEN_CLEAR = 300; //超时时间-本地微信开放平台授权者token清理,单位为秒
}
