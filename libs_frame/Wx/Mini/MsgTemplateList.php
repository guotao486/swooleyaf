<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/8/31 0031
 * Time: 17:15
 */
namespace Wx\Mini;

class MsgTemplateList extends MiniBase {
    public function __construct(){
        parent::__construct();
        $this->offset = 0;
        $this->count = 20;
    }

    private function __clone(){
    }

    /**
     * 位移
     * @var int
     */
    private $offset = 0;
    /**
     * 记录数
     * @var int
     */
    private $count = 0;

    /**
     * @return int
     */
    public function getOffset() : int {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getCount() : int {
        return $this->count;
    }

    /**
     * 设置范围
     * @param int $page
     * @param int $limit
     */
    public function setRange(int $page,int $limit){
        $truePage = $page > 0 ? $page : 1;
        $this->count = ($limit > 0) && ($limit <= 20) ? $limit : 20;
        $this->offset = ($truePage - 1) * $this->count;
    }

    public function getDetail() : array {
        return [
            'offset' => $this->offset,
            'count' => $this->count,
        ];
    }
}