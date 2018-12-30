<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/12/30 0030
 * Time: 10:54
 */
namespace SyMessagePush;

abstract class PushBaseTencent extends PushBase {
    public function __construct(){
        parent::__construct();
    }

    protected function getContent() : array {
    }
}