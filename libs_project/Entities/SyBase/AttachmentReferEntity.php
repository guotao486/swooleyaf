<?php
namespace Entities\SyBase;

use DB\Entities\MysqlEntity;

class AttachmentReferEntity extends MysqlEntity {
    public function __construct() {
        $this->_dbName = 'sy_base';
        parent::__construct($this->_dbName, 'attachment_refer','id');
    }

    /**
     * 引用ID
     * @var int
     */
    public $id = null;

    /**
     * 上传文件名
     * @var string
     */
    public $upload_name = '';

    /**
     * 附件ID
     * @var int
     */
    public $attach_id = 0;

    /**
     * 上传用户ID
     * @var string
     */
    public $uid = '';

    /**
     * 引用时间
     * @var int
     */
    public $created = 0;
}