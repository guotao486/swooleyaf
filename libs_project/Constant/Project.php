<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/3/2 0002
 * Time: 11:30
 */
namespace Constant;

use Traits\SimpleTrait;

final class Project extends ProjectBase {
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

    //REDIS常量 以sy000开头的前缀为框架内部前缀,以sy+3位数字开头的前缀为公共模块前缀
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
