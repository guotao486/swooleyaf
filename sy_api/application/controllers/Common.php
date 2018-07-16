<?php
/**
 * 业务处理公共控制器类
 * User: jw
 * Date: 17-4-5
 * Time: 下午8:34
 */
class CommonController extends \SyFrame\BaseController {
    public $signStatus = true;

    public function init() {
        parent::init();
        $this->signStatus = true;

        $token = \Tool\SySession::getSessionId();
        $_COOKIE[\Constant\Project::DATA_KEY_SESSION_TOKEN] = $token;
        $expireTime = \Tool\Tool::getNowTime() + 604800;
        $totalDomain = \Tool\Tool::getConfig('project.' . SY_ENV . SY_PROJECT . '.domain.cookie');
        if(isset($_SERVER['SY-DOMAIN'])){
            $domain = trim($_SERVER['SY-DOMAIN']);
            if(!in_array($domain, $totalDomain)){
                throw new \Exception\Common\CheckException('请求域名不支持', \Constant\ErrorCode::COMMON_SERVER_ERROR);
            }
        } else {
            $domain = $totalDomain[0];
        }

        \Response\SyResponseHttp::cookie(\Constant\Project::DATA_KEY_SESSION_TOKEN, $token, $expireTime, '/', $domain);
    }
}