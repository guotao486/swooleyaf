<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/5 0005
 * Time: 8:29
 */
namespace DesignPatterns\Facades;

use Constant\ErrorCode;
use Exception\Common\CheckException;
use Traits\SimpleFacadeTrait;

abstract class WxOpenNotifyAuthorizerFacade extends SyBaseFacade {
    use SimpleFacadeTrait;

    public static function __callStatic($funcName, $args){
        $data = parent::checkArgs($args);

        switch ($funcName) {
            case 'acceptNotify':
                $res = static::responseNotify($data);
                break;
            default:
                throw new CheckException('方法不支持', ErrorCode::COMMON_SERVER_ERROR);
        }

        return $res;
    }

    abstract protected static function responseNotify(array $data) : array;
}