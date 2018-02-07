<?php
require_once __DIR__ . '/helper_load.php';

/**
 * 模块注册刷新
 */

$modulePrefix = \DesignPatterns\Singletons\Etcd3Singleton::getInstance()->getPrefixModules();
$registryList = \DesignPatterns\Singletons\Etcd3Singleton::getInstance()->getList($modulePrefix);
if ($registryList === false) {
    exit();
}

$syPack = new \Tool\SyPack();
foreach ($registryList['data'] as $eRegistry) {
    $serverData = \Tool\Tool::jsonDecode($eRegistry['value']);
    if(substr($serverData['module'], 0, 2) == SY_PROJECT){
        if ($serverData['module'] == \Constant\Server::MODULE_NAME_API) {
            $url = 'http://' . $serverData['host'] . ':' . $serverData['port'];
            $syPack->setCommandAndData(\Tool\SyPack::COMMAND_TYPE_SOCKET_CLIENT_SEND_TASK_REQ, [
                'task_module' => $serverData['module'],
                'task_command' => \Constant\Server::TASK_TYPE_REFRESH_SERVER_REGISTRY,
                'task_params' => [],
            ]);
            $packStr = $syPack->packData();
            $syPack->init();
            \Tool\Tool::sendSyHttpTaskReq($url, $packStr);
        } else {
            $syPack->setCommandAndData(\Tool\SyPack::COMMAND_TYPE_RPC_CLIENT_SEND_TASK_REQ, [
                'task_command' => \Constant\Server::TASK_TYPE_REFRESH_SERVER_REGISTRY,
                'task_params' => [],
            ]);
            $packStr = $syPack->packData();
            $syPack->init();
            \Tool\Tool::sendSyRpcReq($serverData['host'], (int)$serverData['port'], $packStr);
        }
    }
}
