<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/5 0005
 * Time: 8:30
 */
namespace DesignPatterns\Facades;

use Constant\ErrorCode;
use Exception\Common\CheckException;
use Traits\SimpleFacadeTrait;

abstract class WxOpenNotifyWxFacade extends SyBaseFacade {
    use SimpleFacadeTrait;

    public static function __callStatic($funcName, $args){
        $data = parent::checkArgs($args);

        switch ($funcName) {
            case 'acceptNotify':
                static::handleNotify($data);
                break;
            default:
                throw new CheckException('方法不支持', ErrorCode::COMMON_SERVER_ERROR);
        }
    }

    abstract protected static function handleNotify(array $data);
}