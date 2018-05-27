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

    public static $totalEnvProject = [
        self::ENV_PROJECT_DEV,
        self::ENV_PROJECT_PRODUCT,
    ];

    public static $totalEnvSystem = [
        self::ENV_SYSTEM_BSD,
        self::ENV_SYSTEM_LINUX,
    ];

    public static $totalImageFilterDither = [
        self::IMAGE_FILTER_DITHER_ORDERED,
        self::IMAGE_FILTER_DITHER_DIFFUSION,
    ];

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

    //进程常量
    const PROCESS_TYPE_TASK = 'Task'; //类型-task
    const PROCESS_TYPE_WORKER = 'Worker'; //类型-worker
    const PROCESS_TYPE_MANAGER = 'Manager'; //类型-manager
    const PROCESS_TYPE_MAIN = 'Main'; //类型-main

    //环境常量
    const ENV_PROJECT_DEV = 'dev'; //项目环境-测试
    const ENV_PROJECT_PRODUCT = 'product'; //项目环境-生产
    const ENV_SYSTEM_BSD = 'FreeBSD'; //系统环境-bsd
    const ENV_SYSTEM_LINUX = 'Linux'; //系统环境-linux

    //版本常量
    const VERSION_PHP_MIN = '7.0.0'; //最低php版本

    //YAC常量,以0000开头的前缀为框架内部前缀,并键名总长度不超过48个字符串
    const YAC_PREFIX_FUSE = '0000'; //前缀-熔断器

    //本地缓存标识
    const CACHE_LOCAL_TAG_PREFIX_WX_COMPONENT_ACCESS_TOKEN = 'a101_'; //前缀-微信开放平台access token

    //熔断器常量
    const FUSE_STATE_OPEN = 'open'; //状态-开启
    const FUSE_STATE_CLOSED = 'closed'; //状态-关闭
    const FUSE_STATE_HALF_OPEN = 'half_open'; //状态-半开
    const FUSE_TIME_ERROR_STAT = 15; //错误统计间隔时间,单位为秒
    const FUSE_TIME_OPEN_KEEP = 10; //开启状态保持时间,单位为秒
    const FUSE_NUM_REQUEST_ERROR = 20; //请求出错次数
    const FUSE_NUM_HALF_REQUEST_SUCCESS = 10; //半开状态请求成功次数
    const FUSE_MSG_REQUEST_ERROR = '{"code":10001,"data":[],"msg":"服务繁忙,清稍后重试"}'; //请求出错提示消息

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
    const IMAGE_FILTER_DITHER_DIFFUSION = 'diffusion'; //噪点滤镜类型-扩散
    const IMAGE_FILTER_DITHER_ORDERED = 'ordered'; //噪点滤镜类型-规整

    //任务常量,4位字符串,数字和字母组成,纯数字的为框架内部任务,其他为自定义任务
    const TASK_TYPE_CLEAR_API_SIGN_CACHE = '0001'; //任务类型-清理api签名缓存
    const TASK_TYPE_CLEAR_LOCAL_USER_CACHE = '0002'; //任务类型-清除本地用户信息缓存
    const TASK_TYPE_CLEAR_LOCAL_WXSHOP_TOKEN_CACHE = '0003'; //任务类型-清除本地微信商户号token缓存
    const TASK_TYPE_CLEAR_LOCAL_WXOPEN_AUTHORIZER_TOKEN_CACHE = '0004'; //任务类型-清除本地微信开放平台授权者token缓存
}
