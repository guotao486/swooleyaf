<?php
require_once __DIR__ . '/helper_load.php';

$projects = include(__DIR__ . '/helper_projects.php');

$syPack = new \Tool\SyPack();
$syPack->setCommandAndData(\Tool\SyPack::COMMAND_TYPE_RPC_CLIENT_SEND_API_REQ, [
    'api_uri' => '/Index/Index/check',
    'api_params' => [],
]);
$checkContent = $syPack->packData();

foreach ($projects as $eProject) {
    if($eProject['module_type'] == 'rpc'){
        foreach ($eProject['listens'] as $eListen) {
            \Tool\Tool::sendSyRpcReq($eListen['host'], $eListen['port'], $checkContent);
        }
    }
}