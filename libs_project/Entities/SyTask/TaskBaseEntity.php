<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-3-5
 * Time: 15:30
 */
namespace Entities\SyTask;

use DB\Entities\MysqlEntity;

class TaskBaseEntity extends MysqlEntity {
    public function __construct() {
        $this->_dbName = 'sy_task';
        parent::__construct($this->_dbName, 'task_base');
    }

    /**
     * 主键ID
     * @var int
     */
    public $id = null;

    /**
     * 任务标识
     * @var string
     */
    public $tag = '';

    /**
     * 任务标题
     * @var string
     */
    public $task_title = '';

    /**
     * 任务描述
     * @var string
     */
    public $task_desc = '';

    /**
     * 任务持久化类型 1:一次性定时任务 2:间隔定时任务 3:cron定时任务
     * @var int
     */
    public $persist_type = 0;

    /**
     * 任务执行对象,外部url请求地址
     * @var string
     */
    public $exec_obj = '';

    /**
     * 任务执行参数,json格式
     * @var string
     */
    public $exec_params = '';

    /**
     * 任务执行时间
     * @var string
     */
    public $exec_time = '';

    /**
     * 开始时间戳,一次性任务为任务执行时间戳,间隔任务为间隔时间秒数,cron任务为创建时间戳
     * @var int
     */
    public $start_time = 0;

    /**
     * 任务状态
     * @var int
     */
    public $status = 0;

    /**
     * 创建时间戳
     * @var int
     */
    public $created = 0;

    /**
     * 修改时间戳
     * @var int
     */
    public $updated = 0;
}