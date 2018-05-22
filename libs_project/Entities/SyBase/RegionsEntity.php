<?php
namespace Entities\SyBase;

use DB\Entities\MysqlEntity;

class RegionsEntity extends MysqlEntity {
    public function __construct() {
        parent::__construct('sy_base', 'regions','tag');
    }

    /**
     * 标识
     * @var string
     */
    public $tag = null;

    /**
     * 级别
     * @var int
     */
    public $level = 0;

    /**
     * 名称
     * @var string
     */
    public $title = '';

    /**
     * 排序
     * @var int
     */
    public $sort = 0;
}