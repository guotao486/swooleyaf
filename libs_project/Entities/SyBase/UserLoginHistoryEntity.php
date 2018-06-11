<?php
namespace Entities\SyBase;

use DB\Entities\MysqlEntity;

class UserLoginHistoryEntity extends MysqlEntity {
    public function __construct() {
        $this->_dbName = 'sy_base';
        parent::__construct($this->_dbName, 'user_login_history','id');
    }

    /**
     *
     * @var int
     */
    public $id = null;

    /**
     * 用户ID
     * @var string
     */
    public $uid = '';

    /**
     * 登录类型
     * @var string
     */
    public $type = '';

    /**
     * 登录IP
     * @var string
     */
    public $ip = '';

    /**
     * 创建时间戳
     * @var int
     */
    public $created = 0;
}