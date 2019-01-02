<?php
if(!defined('SY_ADD_TIMER')){
    define('SY_ADD_TIMER', false);
}

$configs = \Tool\Tool::getConfig('project.' . SY_ENV . SY_PROJECT);
if(isset($configs['aliopen'])){
    $proxyStatus = (int)\Tool\Tool::getArrayVal($configs, 'aliopen.proxy.status', 0, true);
    if ($proxyStatus > 0) {
        define('ENABLE_HTTP_PROXY', true);
    } else {
        define('ENABLE_HTTP_PROXY', false);
    }
    $proxyIp = (string)\Tool\Tool::getArrayVal($configs, 'aliopen.proxy.ip', '127.0.0.1', true);
    if(preg_match('/^(\.(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])){4}$/', '.' . $proxyIp) > 0){
        define('HTTP_PROXY_IP', $proxyIp);
    } else {
        throw new \Exception\Common\CheckException('代理IP不合法', \Constant\ErrorCode::COMMON_SERVER_ERROR);
    }
    $proxyPort = (int)\Tool\Tool::getArrayVal($configs, 'aliopen.proxy.port', 0, true);
    if(($proxyPort > 1000) && ($proxyPort <= 65535)){
        define('HTTP_PROXY_PORT', $proxyPort);
    } else {
        throw new \Exception\Common\CheckException('代理端口不合法', \Constant\ErrorCode::COMMON_SERVER_ERROR);
    }
}
