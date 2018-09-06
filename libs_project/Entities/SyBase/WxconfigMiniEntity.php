<?php
namespace Entities\SyBase;

use DB\Entities\MysqlEntity;

class WxconfigMiniEntity extends MysqlEntity {
    public function __construct(string $dbName='') {
        $this->_dbName = isset($dbName{0}) ? $dbName : 'sy_base';
        parent::__construct($this->_dbName, 'wxconfig_mini', 'id');
    }

    /**
     * 
     * @var int
     */
    public $id = null;

    /**
     * 微信公众号ID
     * @var string
     */
    public $app_id = '';

    /**
     * 小程序类型 1:平台 2:商户
     * @var int
     */
    public $wtype = 1;

    /**
     * 用户ID
     * @var string
     */
    public $user_id = '';

    /**
     * 最新代码版本
     * @var string
     */
    public $latest_code = '';

    /**
     * 审核ID
     * @var string
     */
    public $audit_id = '';

    /**
     * 审核状态 -1:未提交审核 0:审核成功 1:审核失败 2:审核中
     * @var int
     */
    public $audit_status = -1;

    /**
     * 审核描述
     * @var string
     */
    public $audit_desc = '';

    /**
     * 操作状态
     * @var int
     */
    public $option_status = 1;

    /**
     * 状态
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
