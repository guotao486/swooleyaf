<?php
require_once __DIR__ . '/helper_load.php';

$projectName = \Tool\Tool::getClientOption('-n');
if(!$projectName){
    print_r('参数 -n 服务项目名称无效,必须与项目目录相同,否则无法加载 profile文件' . PHP_EOL);
    exit(1);
}

$projectProfile = SY_ROOT . '/' . $projectName . '/profile.php';
if (is_file($projectProfile)) {
    require_once $projectProfile;
} else {
    print_r($projectName . ' profile file not exist' . PHP_EOL);
    exit(2);
}

$moduleName = trim(\Tool\Tool::getClientOption('-module'));
if (strlen($moduleName) == 0) {
    print_r('module name must exist' . PHP_EOL);
    exit(3);
} else if (!in_array($moduleName, \Constant\Server::$totalModules)) {
    print_r('module name error' . PHP_EOL);
    exit(4);
}
define('SY_MODULE', $moduleName);

$port = trim(\Tool\Tool::getClientOption('-port'));
if (strlen($port) == 0) {
    print_r('port must exist' . PHP_EOL);
    exit(5);
} else if (!is_numeric($port)) {
    print_r('port must is numeric' . PHP_EOL);
    exit(6);
} else {
    $truePort = (int)$port;
}

if($moduleName == \Constant\Server::MODULE_NAME_API){
    $server = new \SyServer\HttpServer($truePort);
} else {
    $server = new \SyServer\RpcServer($truePort);
}

$action = \Tool\Tool::getClientOption('-s', false, 'start');
switch ($action) {
    case 'start' :
        $server->start();
        break;
    case 'stop' :
        $server->stop();
        break;
    case 'restart' :
        $server->stop();
        sleep(1);
        $server->start();
        break;
    default :
        $server->help();
}