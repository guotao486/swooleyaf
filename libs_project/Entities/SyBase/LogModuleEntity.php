<?php
namespace Entities\SyBase;

use DB\Entities\MysqlEntity;

class LogModuleEntity extends MysqlEntity {
    public function __construct() {
        $this->_dbName = 'sy_base';
        parent::__construct($this->_dbName, 'log_module','id');
    }

    /**
     *
     * @var int
     */
    public $id = null;

    /**
     * 日志级别
     * @var string
     */
    public $log_level = '';

    /**
     * 服务端ip
     * @var string
     */
    public $server_ip = '';

    /**
     * 模块名称
     * @var string
     */
    public $log_module = '';

    /**
     * 日志内容
     * @var string
     */
    public $log_content = '';

    /**
     * 创建毫秒级时间戳
     * @var double
     */
    public $created = 0.00;
}