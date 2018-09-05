<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/5 0005
 * Time: 16:08
 */
namespace DesignPatterns\Facades\UserLogin;

use Constant\ErrorCode;
use DesignPatterns\Facades\UserLoginFacade;
use Exception\Common\CheckException;
use Request\SyRequest;
use Traits\SimpleFacadeTrait;

class WxAuthBase extends UserLoginFacade {
    use SimpleFacadeTrait;

    protected static function checkParams(array $data) : array {
        $wxCode = trim(SyRequest::getParams('wx_code', ''));
        $redirectUrl = (string)SyRequest::getParams('redirect_url', '');
        if (strlen($wxCode) == 0) {
            throw new CheckException('微信授权码不能为空', ErrorCode::COMMON_PARAM_ERROR);
        } else if (strlen($redirectUrl) == 0) {
            throw new CheckException('回跳URL不能为空', ErrorCode::COMMON_PARAM_ERROR);
        }

        return [
            'wx_code' => $wxCode,
            'redirect_url' => $redirectUrl,
        ];
    }

    protected static function login(array $data) : array {
        return [];
    }
}