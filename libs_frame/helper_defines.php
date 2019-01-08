<?php
//是否开启swoole的定时任务
if(!defined('SY_TIMER')){
    define('SY_TIMER', false);
}
//是否连接数据库
if(!defined('SY_DATABASE')){
    define('SY_DATABASE', true);
}
//是否本地缓存微信账号数据
if(!defined('SY_CACHE_WXACCOUNT')){
    define('SY_CACHE_WXACCOUNT', false);
}
//是否本地缓存微信开放平台账号数据
if(!defined('SY_CACHE_WXOPEN')){
    define('SY_CACHE_WXOPEN', false);
}

$configs = \Tool\Tool::getConfig('project.' . SY_ENV . SY_PROJECT);
$proxyStatus = (int)\Tool\Tool::getArrayVal($configs, 'aliopen.proxy.status', 0, true);
if ($proxyStatus > 0) {
    define('ALIOPEN_ENABLE_HTTP_PROXY', true);
} else {
    define('ALIOPEN_ENABLE_HTTP_PROXY', false);
}
$proxyIp = (string)\Tool\Tool::getArrayVal($configs, 'aliopen.proxy.ip', '127.0.0.1', true);
if(preg_match('/^(\.(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])){4}$/', '.' . $proxyIp) > 0){
    define('ALIOPEN_HTTP_PROXY_IP', $proxyIp);
} else {
    throw new \Exception\Common\CheckException('代理IP不合法', \Constant\ErrorCode::COMMON_SERVER_ERROR);
}
$proxyPort = (int)\Tool\Tool::getArrayVal($configs, 'aliopen.proxy.port', 8888, true);
if(($proxyPort > 1000) && ($proxyPort <= 65535)){
    define('ALIOPEN_HTTP_PROXY_PORT', $proxyPort);
} else {
    throw new \Exception\Common\CheckException('代理端口不合法', \Constant\ErrorCode::COMMON_SERVER_ERROR);
}
