<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-09
 * Time: 0:42
 */
namespace Exception\Ali;

use Exception\BaseException;

class AliShopException extends BaseException {
    public function __construct($message, $code) {
        parent::__construct($message, $code);
        $this->tipName = '支付宝店铺异常';
    }
}