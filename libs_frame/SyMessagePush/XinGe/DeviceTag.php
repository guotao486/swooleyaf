<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/12/30 0030
 * Time: 11:39
 */
namespace SyMessagePush\XinGe;

use SyMessagePush\PushBaseXinGe;

class DeviceTag extends PushBaseXinGe {
    public function __construct(){
        parent::__construct();
    }

    private function __clone(){
    }

    public function getDetail() : array {
    }
}