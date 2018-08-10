<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 18-3-10
 * Time: 下午1:15
 */
namespace Dao;

use Constant\ErrorCode;
use Constant\Project;
use Exception\Common\CheckException;
use Request\SyRequest;
use Tool\Tool;
use Traits\SimpleDaoTrait;

class LoginDao {
    use SimpleDaoTrait;

    private static $loginCheckMap = [
        Project::LOGIN_TYPE_PHONE => 'loginCheckPhone',
        Project::LOGIN_TYPE_EMAIL => 'loginCheckEmail',
        Project::LOGIN_TYPE_ACCOUNT => 'loginCheckAccount',
        Project::LOGIN_TYPE_WX_AUTH_BASE => 'loginCheckWxAuthBase',
        Project::LOGIN_TYPE_WX_AUTH_USER => 'loginCheckWxAuthUser',
        Project::LOGIN_TYPE_WX_SCAN => 'loginCheckWxScan',
        Project::LOGIN_TYPE_QQ => 'loginCheckQQ',
    ];
    private static $loginHandleMap = [
        Project::LOGIN_TYPE_PHONE => 'loginHandlePhone',
        Project::LOGIN_TYPE_EMAIL => 'loginHandleEmail',
        Project::LOGIN_TYPE_ACCOUNT => 'loginHandleAccount',
        Project::LOGIN_TYPE_WX_AUTH_BASE => 'loginHandleWxAuthBase',
        Project::LOGIN_TYPE_WX_AUTH_USER => 'loginHandleWxAuthUser',
        Project::LOGIN_TYPE_WX_SCAN => 'loginHandleWxScan',
        Project::LOGIN_TYPE_QQ => 'loginHandleQQ',
    ];

    private static function loginCheckPhone(array &$data) {
        $phone = trim(SyRequest::getParams('user_phone', ''));
        $pwd = (string)SyRequest::getParams('user_pwd', '');
        if (strlen($phone) == 0) {
            throw new CheckException('手机号码不能为空', ErrorCode::COMMON_PARAM_ERROR);
        } else if (strlen($pwd) == 0) {
            throw new CheckException('密码不能为空', ErrorCode::COMMON_PARAM_ERROR);
        }

        $data['user_phone'] = $phone;
        $data['user_pwd'] = $pwd;
    }

    private static function loginCheckEmail(array &$data) {
        $email = trim(SyRequest::getParams('user_email', ''));
        $pwd = (string)SyRequest::getParams('user_pwd', '');
        if (strlen($email) == 0) {
            throw new CheckException('邮箱不能为空', ErrorCode::COMMON_PARAM_ERROR);
        } else if (strlen($pwd) == 0) {
            throw new CheckException('密码不能为空', ErrorCode::COMMON_PARAM_ERROR);
        }

        $data['user_email'] = $email;
        $data['user_pwd'] = $pwd;
    }

    private static function loginCheckAccount(array &$data) {
        $account = trim(SyRequest::getParams('user_account', ''));
        $pwd = (string)SyRequest::getParams('user_pwd', '');
        if (strlen($account) == 0) {
            throw new CheckException('账号不能为空', ErrorCode::COMMON_PARAM_ERROR);
        } else if (strlen($pwd) == 0) {
            throw new CheckException('密码不能为空', ErrorCode::COMMON_PARAM_ERROR);
        }

        $data['user_account'] = $account;
        $data['user_pwd'] = $pwd;
    }

    private static function loginCheckWxAuthBase(array &$data) {
        $wxCode = trim(SyRequest::getParams('wx_code', ''));
        $redirectUrl = (string)SyRequest::getParams('redirect_url', '');
        if (strlen($wxCode) == 0) {
            throw new CheckException('微信授权码不能为空', ErrorCode::COMMON_PARAM_ERROR);
        } else if (strlen($redirectUrl) == 0) {
            throw new CheckException('回跳URL不能为空', ErrorCode::COMMON_PARAM_ERROR);
        }

        $data['wx_code'] = $wxCode;
        $data['redirect_url'] = $redirectUrl;
    }

    private static function loginCheckWxAuthUser(array &$data) {
        $wxCode = trim(SyRequest::getParams('wx_code', ''));
        $redirectUrl = (string)SyRequest::getParams('redirect_url', '');
        if (strlen($wxCode) == 0) {
            throw new CheckException('微信授权码不能为空', ErrorCode::COMMON_PARAM_ERROR);
        } else if (strlen($redirectUrl) == 0) {
            throw new CheckException('回跳URL不能为空', ErrorCode::COMMON_PARAM_ERROR);
        }

        $data['wx_code'] = $wxCode;
        $data['redirect_url'] = $redirectUrl;
    }

    private static function loginCheckWxScan(array &$data) {
        $wxCode = trim(SyRequest::getParams('wx_code', ''));
        $redirectUrl = (string)SyRequest::getParams('redirect_url', '');
        if (strlen($wxCode) == 0) {
            throw new CheckException('微信授权码不能为空', ErrorCode::COMMON_PARAM_ERROR);
        } else if (strlen($redirectUrl) == 0) {
            throw new CheckException('回跳URL不能为空', ErrorCode::COMMON_PARAM_ERROR);
        }

        $data['wx_code'] = $wxCode;
        $data['redirect_url'] = $redirectUrl;
    }

    private static function loginCheckQQ(array &$data) {
        $qq = trim(SyRequest::getParams('user_qq', ''));
        $pwd = (string)SyRequest::getParams('user_pwd', '');
        if (strlen($qq) == 0) {
            throw new CheckException('QQ号码不能为空', ErrorCode::COMMON_PARAM_ERROR);
        } else if (strlen($pwd) == 0) {
            throw new CheckException('密码不能为空', ErrorCode::COMMON_PARAM_ERROR);
        }

        $data['user_qq'] = $qq;
        $data['user_pwd'] = $pwd;
    }

    private static function loginHandlePhone(array $data) {
        return [];
    }

    private static function loginHandleEmail(array $data) {
        return [];
    }

    private static function loginHandleAccount(array $data) {
        return [];
    }

    private static function loginHandleWxAuthBase(array $data) {
        return [];
    }

    private static function loginHandleWxAuthUser(array $data) {
        return [];
    }

    private static function loginHandleWxScan(array $data) {
        return [];
    }

    private static function loginHandleQQ(array $data) {
        return [];
    }

    public static function login(array $data) {
        $checkFunc = Tool::getArrayVal(self::$loginCheckMap, $data['login_type'], null);
        if (is_null($checkFunc)) {
            throw new CheckException('登录类型不支持', ErrorCode::COMMON_PARAM_ERROR);
        }
        self::$checkFunc($data);

        $handleFunc = Tool::getArrayVal(self::$loginHandleMap, $data['login_type'], null);
        return self::$handleFunc($data);
    }
}