<?php
namespace Entities\SyBase;

use DB\Entities\MysqlEntity;

class WithdrawHistoryEntity extends MysqlEntity {
    public function __construct(string $dbName='') {
        $this->_dbName = isset($dbName{0}) ? $dbName : 'sy_base';
        parent::__construct($this->_dbName, 'withdraw_history','id');
    }

    /**
     *
     * @var int
     */
    public $id = null;

    /**
     * 提现单号
     * @var string
     */
    public $wsn = '';

    /**
     * 操作者用户ID
     * @var string
     */
    public $uid = '';

    /**
     * 操作标题
     * @var string
     */
    public $option_title = '';

    /**
     * 操作类型
     * @var int
     */
    public $option_type = 0;

    /**
     * 操作内容
     * @var string
     */
    public $option_content = '';

    /**
     * 创建时间戳
     * @var int
     */
    public $created = 0;
}