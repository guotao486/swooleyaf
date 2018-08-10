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
        self::MODULE_NAME_ORDER,
        self::MODULE_NAME_USER,
        self::MODULE_NAME_SERVICE,
    ];
    public static $totalModuleBase = [
        self::MODULE_BASE_API,
        self::MODULE_BASE_ORDER,
        self::MODULE_BASE_USER,
        self::MODULE_BASE_SERVICE,
    ];
    const MODULE_BASE_API = 'api';
    const MODULE_BASE_ORDER = 'order';
    const MODULE_BASE_USER = 'user';
    const MODULE_BASE_SERVICE = 'services';
    const MODULE_NAME_API = SY_PROJECT . self::MODULE_BASE_API;
    const MODULE_NAME_ORDER = SY_PROJECT . self::MODULE_BASE_ORDER;
    const MODULE_NAME_USER = SY_PROJECT . self::MODULE_BASE_USER;
    const MODULE_NAME_SERVICE = SY_PROJECT . self::MODULE_BASE_SERVICE;

    //数据常量
    const DATA_KEY_SESSION_TOKEN = 'sytoken'; //键名-session标识
    const DATA_KEY_CACHE_UNIQUE_ID = self::REDIS_PREFIX_UNIQUE_ID . 'uniqueid'; //键名-缓存唯一ID

    //校验器常量
    const VALIDATOR_STRING_TYPE_REQUIRED = 'string_required'; //字符串类型-必填
    const VALIDATOR_STRING_TYPE_MIN = 'string_min'; //字符串类型-最小长度
    const VALIDATOR_STRING_TYPE_MAX = 'string_max'; //字符串类型-最大长度
    const VALIDATOR_STRING_TYPE_REGEX = 'string_regex'; //字符串类型-正则表达式
    const VALIDATOR_STRING_TYPE_PHONE = 'string_phone'; //字符串类型-手机号码
    const VALIDATOR_STRING_TYPE_TEL = 'string_tel'; //字符串类型-联系方式
    const VALIDATOR_STRING_TYPE_EMAIL = 'string_email'; //字符串类型-邮箱
    const VALIDATOR_STRING_TYPE_URL = 'string_url'; //字符串类型-URL链接
    const VALIDATOR_STRING_TYPE_JSON = 'string_json'; //字符串类型-JSON
    const VALIDATOR_STRING_TYPE_SIGN = 'string_sign'; //字符串类型-请求签名
    const VALIDATOR_STRING_TYPE_BASE_IMAGE = 'string_baseimage'; //字符串类型-base64编码图片
    const VALIDATOR_STRING_TYPE_IP = 'string_ip'; //字符串类型-IP
    const VALIDATOR_STRING_TYPE_LNG = 'string_lng'; //字符串类型-经度
    const VALIDATOR_STRING_TYPE_LAT = 'string_lat'; //字符串类型-纬度
    const VALIDATOR_STRING_TYPE_NO_JS = 'string_nojs'; //字符串类型-不允许js脚本
    const VALIDATOR_STRING_TYPE_NO_EMOJI = 'string_noemoji'; //字符串类型-不允许emoji表情
    const VALIDATOR_STRING_TYPE_ZH = 'string_zh'; //字符串类型-中文,数字,字母
    const VALIDATOR_INT_TYPE_REQUIRED = 'int_required'; //整数类型-必填
    const VALIDATOR_INT_TYPE_MIN = 'int_min'; //整数类型-最小值
    const VALIDATOR_INT_TYPE_MAX = 'int_max'; //整数类型-最大值
    const VALIDATOR_INT_TYPE_IN = 'int_in'; //整数类型-取值枚举
    const VALIDATOR_INT_TYPE_BETWEEN = 'int_between'; //整数类型-取值区间
    const VALIDATOR_DOUBLE_TYPE_REQUIRED = 'double_required'; //浮点数类型-必填
    const VALIDATOR_DOUBLE_TYPE_MIN = 'double_min'; //浮点数类型-最小值
    const VALIDATOR_DOUBLE_TYPE_MAX = 'double_max'; //浮点数类型-最大值
    const VALIDATOR_DOUBLE_TYPE_BETWEEN = 'double_between'; //浮点数类型-取值区间

    //公共常量
    const COMMON_PAGE_DEFAULT = 1; //默认页数
    const COMMON_LIMIT_DEFAULT = 10; //默认分页限制

    //REDIS常量 以sy000开头的前缀为框架内部前缀,以sy+3位数字开头的前缀为公共模块前缀
    const REDIS_PREFIX_SESSION = 'sy000001_'; //前缀-session
    const REDIS_PREFIX_CODE_IMAGE = 'sy001000_'; //前缀-验证码图片
    const REDIS_PREFIX_UNIQUE_ID = 'sy001001_'; //前缀-唯一ID
    const REDIS_PREFIX_MESSAGE_QUEUE = 'sy001002_'; //前缀-消息队列
    const REDIS_PREFIX_IMAGE_DATA = 'sy001003_'; //前缀-图片缓存
    const REDIS_PREFIX_IM_ADMIN = 'sy001004_'; //前缀-im管理账号缓存
    const REDIS_PREFIX_WX_ACCOUNT = 'sy002000_'; //前缀-微信公众号
    const REDIS_PREFIX_WX_COMPONENT_ACCOUNT = 'sy002001_'; //前缀-微信开放平台账号
    const REDIS_PREFIX_WX_COMPONENT_AUTHORIZER = 'sy002002_'; //前缀-微信开放平台授权公众号
    const REDIS_PREFIX_WX_NATIVE_PRE = 'sy002003_'; //前缀-微信扫码预支付
    const REDIS_PREFIX_ROLE_POWERS = 'sya01001_'; //前缀-角色权限列表
    const REDIS_PREFIX_ROLE_LIST = 'sya01002_'; //前缀-角色列表
    const REDIS_PREFIX_REGION_LIST = 'sya01003_'; //前缀-地区缓存

    //消息队列常量
    const MESSAGE_QUEUE_TOPIC_REDIS_ADD_LOG = 'a000'; //redis主题-添加日志
    const MESSAGE_QUEUE_TOPIC_REDIS_REQ_HEALTH_CHECK = 'a001'; //redis主题-请求健康检查

    //支付常量
    const PAY_WAY_WX = 1; //方式-微信
    const PAY_WAY_ALI = 2; //方式-支付宝
    const PAY_TYPE_WX_JS = 'a000'; //类型-微信公众号js支付
    const PAY_TYPE_WX_NATIVE_DYNAMIC = 'a001'; //类型-微信公众号动态扫码支付
    const PAY_TYPE_WX_NATIVE_STATIC = 'a002'; //类型-微信公众号静态扫码支付
    const PAY_TYPE_WX_MINI_JS = 'a003'; //类型-微信小程序js支付
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

    //任务常量,4位字符串,数字和字母组成,纯数字的为框架内部任务,其他为自定义任务
    const TASK_TYPE_CLEAR_API_SIGN_CACHE = '0001'; //任务类型-清理api签名缓存
    const TASK_TYPE_CLEAR_LOCAL_USER_CACHE = '0002'; //任务类型-清除本地用户信息缓存
    const TASK_TYPE_CLEAR_LOCAL_WXSHOP_TOKEN_CACHE = '0003'; //任务类型-清除本地微信商户号token缓存
    const TASK_TYPE_CLEAR_LOCAL_WXOPEN_AUTHORIZER_TOKEN_CACHE = '0004'; //任务类型-清除本地微信开放平台授权者token缓存

    //kafka常量
    const KAFKA_TOPIC_TEST = SY_ENV . SY_PROJECT . '0000'; //主题-测试

    //订单常量
    const ORDER_PAY_TYPE_GOODS = '1000'; //支付类型-商品
    const ORDER_REFUND_TYPE_GOODS = '5000'; //退款类型-商品

    //地区常量
    public static $totalRegionLevelType = [
        self::REGION_LEVEL_TYPE_PROVINCE => '省',
        self::REGION_LEVEL_TYPE_CITY => '市',
        self::REGION_LEVEL_TYPE_COUNTY => '县',
    ];
    const REGION_LEVEL_TYPE_PROVINCE = 1; //地区类型-省
    const REGION_LEVEL_TYPE_CITY = 2; //地区类型-市
    const REGION_LEVEL_TYPE_COUNTY = 3; //地区类型-县

    //角色常量
    public static $totalRoleStatus = [
        self::ROLE_STATUS_DELETE => '已删除',
        self::ROLE_STATUS_INVALID => '无效',
        self::ROLE_STATUS_VALID => '有效',
    ];
    const ROLE_STATUS_DELETE = -1; //状态-已删除
    const ROLE_STATUS_INVALID = 0; //状态-无效
    const ROLE_STATUS_VALID = 1; //状态-有效

    //角色权限常量
    public static $totalRolePowerLevel = [
        self::ROLE_POWER_LEVEL_ONE => '第一级',
        self::ROLE_POWER_LEVEL_TWO => '第二级',
        self::ROLE_POWER_LEVEL_THREE => '第三级',
    ];
    const ROLE_POWER_LEVEL_ONE = 1; //层级-第一级
    const ROLE_POWER_LEVEL_TWO = 2; //层级-第二级
    const ROLE_POWER_LEVEL_THREE = 3; //层级-第三级

    //登录常量
    const LOGIN_TYPE_PHONE = 'a000'; //类型-手机号码
    const LOGIN_TYPE_EMAIL = 'a001'; //类型-邮箱
    const LOGIN_TYPE_ACCOUNT = 'a002'; //类型-账号
    const LOGIN_TYPE_WX_AUTH_BASE = 'a100'; //类型-微信静默授权
    const LOGIN_TYPE_WX_AUTH_USER = 'a101'; //类型-微信手动授权
    const LOGIN_TYPE_WX_SCAN = 'a102'; //类型-微信扫码
    const LOGIN_TYPE_QQ = 'a200'; //类型-QQ
}
