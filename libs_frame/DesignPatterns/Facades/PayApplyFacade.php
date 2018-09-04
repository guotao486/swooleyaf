<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-5
 * Time: 上午12:07
 */
namespace DesignPatterns\Facades;

use Constant\ErrorCode;
use Exception\Common\CheckException;
use Traits\SimpleFacadeTrait;

abstract class PayApplyFacade {
    use SimpleFacadeTrait;

    public static function __callStatic($funcName, $args){
        switch ($funcName) {
            case 'handleCheckParams':
                $res = static::checkParams($args);
                break;
            case 'handleApply':
                $res = static::apply($args);
                break;
            default:
                throw new CheckException('方法不支持', ErrorCode::COMMON_SERVER_ERROR);
        }

        return $res;
    }

    abstract protected static function checkParams(array $data) : array;
    abstract protected static function apply(array $data) : array;
}