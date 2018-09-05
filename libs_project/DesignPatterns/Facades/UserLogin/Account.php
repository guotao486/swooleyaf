<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/5 0005
 * Time: 16:06
 */
namespace DesignPatterns\Facades\UserLogin;

use Constant\ErrorCode;
use DesignPatterns\Facades\UserLoginFacade;
use Exception\Common\CheckException;
use Request\SyRequest;
use Traits\SimpleFacadeTrait;

class Account extends UserLoginFacade {
    use SimpleFacadeTrait;

    protected static function checkParams(array $data) : array {
        $account = trim(SyRequest::getParams('user_account', ''));
        $pwd = (string)SyRequest::getParams('user_pwd', '');
        if (strlen($account) == 0) {
            throw new CheckException('账号不能为空', ErrorCode::COMMON_PARAM_ERROR);
        } else if (strlen($pwd) == 0) {
            throw new CheckException('密码不能为空', ErrorCode::COMMON_PARAM_ERROR);
        }

        return [
            'user_account' => $account,
            'user_pwd' => $pwd,
        ];
    }

    protected static function login(array $data) : array {
        return [];
    }
}