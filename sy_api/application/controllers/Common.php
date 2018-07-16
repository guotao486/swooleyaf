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
        $reqOrigin = isset($_SERVER['ORIGIN']) ? trim($_SERVER['ORIGIN']) : '';
        $needIndex = strlen($reqOrigin) > 0 ? strpos($reqOrigin, '.') : false;
        if($needIndex === false){
            $domain = $totalDomain[0];
        } else {
            $domain = substr($reqOrigin, $needIndex);
            if(!in_array($domain, $totalDomain)){
                throw new \Exception\Common\CheckException('请求域名不支持', \Constant\ErrorCode::COMMON_SERVER_ERROR);
            }
        }

        \Response\SyResponseHttp::cookie(\Constant\Project::DATA_KEY_SESSION_TOKEN, $token, $expireTime, '/', $domain);
    }
}