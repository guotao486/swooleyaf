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
    ];
    public static $totalModuleBase = [
        self::MODULE_BASE_API,
    ];
    const MODULE_BASE_API = 'api';
    const MODULE_NAME_API = SY_PROJECT . self::MODULE_BASE_API;

    //REDIS常量 以sy000开头的前缀为框架内部前缀,以sy+3位数字开头的前缀为公共模块前缀
    const REDIS_PREFIX_TIMER_QUEUE = 'sya02000_'; //前缀-定时器队列
    const REDIS_PREFIX_TIMER_CONTENT = 'sya02001_'; //前缀-定时器内容

    //任务常量
    public static $totalTaskStatus = [
        self::TASK_STATUS_DELETE => '已删除',
        self::TASK_STATUS_INVALID => '无效',
        self::TASK_STATUS_VALID => '有效',
    ];
    public static $totalTaskPersistType = [
        self::TASK_PERSIST_TYPE_SINGLE => '单次任务',
        self::TASK_PERSIST_TYPE_INTERVAL => '间隔任务',
    ];
    const TASK_STATUS_DELETE = -1; //任务状态-已删除
    const TASK_STATUS_INVALID = 0; //任务状态-无效
    const TASK_STATUS_VALID = 1; //任务状态-有效
    const TASK_PERSIST_TYPE_SINGLE = 1; //持久化类型-单次
    const TASK_PERSIST_TYPE_INTERVAL = 2; //持久化类型-间隔时间
    const TASK_CACHE_EXPIRE_TIME = 300; //缓存过期时间-五分钟

    //消息队列常量
    const MESSAGE_QUEUE_TOPIC_ADD_LOG = 'a000'; //主题-添加日志
    const MESSAGE_QUEUE_TOPIC_REQ_HEALTH_CHECK = 'a001'; //主题-请求健康检查

    //kafka常量
    const KAFKA_TOPIC_TEST = SY_ENV . SY_PROJECT . '0000'; //主题-测试
}
