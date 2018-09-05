<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/5 0005
 * Time: 9:06
 */
namespace DesignPatterns\Facades;

use Constant\ErrorCode;
use Exception\Common\CheckException;
use Traits\SimpleFacadeTrait;

abstract class SyBaseFacade {
    use SimpleFacadeTrait;

    protected static function checkArgs($args) : array {
        $data = $args[0] ?? null;
        if(!is_array($data)){
            throw new CheckException('数据不合法', ErrorCode::COMMON_SERVER_ERROR);
        }

        return $data;
    }
}