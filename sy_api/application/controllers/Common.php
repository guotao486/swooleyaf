<?php
/**
 * 业务处理公共控制器类
 * User: jw
 * Date: 17-4-5
 * Time: 下午8:34
 */
class CommonController extends \SyFrame\BaseController {
    public $signStatus = false;

    public function init() {
        parent::init();
        $this->signStatus = false;

        $token = \Tool\SySession::getSessionId();
        $_COOKIE[\Constant\Server::SERVER_DATA_KEY_TOKEN] = $token;
        $expireTime = time() + 604800;
        $domain = \SyServer\HttpServer::getServerConfig('cookiedomain_base', '');
        \Response\SyResponseHttp::cookie(\Constant\Server::SERVER_DATA_KEY_TOKEN, $token, $expireTime, '/', $domain);
    }
}