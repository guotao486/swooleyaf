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
    const REDIS_PREFIX_CODE_IMAGE = 'sya01000_'; //前缀-验证码图片
    const REDIS_PREFIX_IMAGE_DATA = 'sya01001_'; //前缀-图片缓存
    const REDIS_PREFIX_PAY_HASH = 'sya01002_'; //前缀-支付哈希
    const REDIS_PREFIX_WX_NATIVE_PRE = 'sya01003_'; //前缀-微信扫码预支付
    const REDIS_PREFIX_IM_ADMIN = 'sya01004_'; //前缀-im管理账号缓存
    const REDIS_PREFIX_ROLE_POWERS = 'sya01005_'; //前缀-角色权限列表
    const REDIS_PREFIX_ROLE_LIST = 'sya01006_'; //前缀-角色列表
    const REDIS_PREFIX_REGION_LIST = 'sya01007_'; //前缀-地区缓存

    //消息队列常量
    const MESSAGE_QUEUE_TOPIC_REDIS_ADD_LOG = 'a000'; //redis主题-添加日志
    const MESSAGE_QUEUE_TOPIC_REDIS_REQ_HEALTH_CHECK = 'a001'; //redis主题-请求健康检查

    //支付常量
    const PAY_WAY_WX = 1; //方式-微信
    const PAY_WAY_ALI = 2; //方式-支付宝
    const PAY_TYPE_WX_SHOP_JS = 'a000'; //类型-微信公众号js支付
    const PAY_TYPE_WX_SHOP_NATIVE_DYNAMIC = 'a001'; //类型-微信公众号动态扫码支付
    const PAY_TYPE_WX_SHOP_NATIVE_STATIC = 'a002'; //类型-微信公众号静态扫码支付
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

    //微信小程序常量
    public static $totalWxMiniStatus = [
        self::WXMINI_STATUS_DISABLE => '无效',
        self::WXMINI_STATUS_ENABLE => '有效',
    ];
    public static $totalWxMiniType = [
        self::WXMINI_TYPE_PLAT_MINI => '平台小程序',
        self::WXMINI_TYPE_SHOP_MINI => '商户小程序',
    ];
    public static $totalWxMiniAuditStatus = [
        self::WXMINI_AUDIT_STATUS_UNDO => '未提交审核',
        self::WXMINI_AUDIT_STATUS_SUCCESS => '审核成功',
        self::WXMINI_AUDIT_STATUS_FAIL => '审核失败',
        self::WXMINI_AUDIT_STATUS_HANDING => '审核中',
    ];
    public static $totalWxMiniOptionStatus = [
        self::WXMINI_OPTION_STATUS_UN_UPLOAD => '未上传',
        self::WXMINI_OPTION_STATUS_UPLOADED => '已上传',
        self::WXMINI_OPTION_STATUS_APPLY_AUDIT => '审核中',
        self::WXMINI_OPTION_STATUS_AUDIT_SUCCESS => '审核成功',
        self::WXMINI_OPTION_STATUS_AUDIT_FAIL => '审核失败',
        self::WXMINI_OPTION_STATUS_RELEASED => '已发布',
    ];
    const WXMINI_STATUS_DISABLE = 0; //小程序状态-无效
    const WXMINI_STATUS_ENABLE = 1; //小程序状态-有效
    const WXMINI_TYPE_PLAT_MINI = 1; //小程序类型-平台小程序
    const WXMINI_TYPE_SHOP_MINI = 2; //小程序类型-商户小程序
    const WXMINI_AUDIT_STATUS_UNDO = -1; //小程序审核状态-未提交审核
    const WXMINI_AUDIT_STATUS_SUCCESS = 0; //小程序审核状态-审核成功
    const WXMINI_AUDIT_STATUS_FAIL = 1; //小程序审核状态-审核失败
    const WXMINI_AUDIT_STATUS_HANDING = 2; //小程序审核状态-审核中
    const WXMINI_OPTION_STATUS_UN_UPLOAD = 1; //小程序操作状态-未上传
    const WXMINI_OPTION_STATUS_UPLOADED = 2; //小程序操作状态-已上传
    const WXMINI_OPTION_STATUS_APPLY_AUDIT = 3; //小程序操作状态-审核中
    const WXMINI_OPTION_STATUS_AUDIT_SUCCESS = 4; //小程序操作状态-审核成功
    const WXMINI_OPTION_STATUS_AUDIT_FAIL = 5; //小程序操作状态-审核失败
    const WXMINI_OPTION_STATUS_RELEASED = 6; //小程序操作状态-已发布
    const WXMINI_EXPIRE_TOKEN = 7000; //小程序token超时时间,单位为秒
    const WXMINI_DEFAULT_CLIENT_IP = '127.0.0.1'; //默认客户端IP
}
