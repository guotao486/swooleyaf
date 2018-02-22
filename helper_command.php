<?php
require_once __DIR__ . '/helper_load.php';

$action = trim(\Tool\Tool::getClientOption('-action'));
switch ($action) {
    case 'refreshserver':
        $activeModules = \Tool\Tool::getProjectModulesByRedis(SY_PROJECT);
        if (!empty($activeModules)) {
            \Tool\Tool::updateProjectModules($activeModules);
        }

        break;
    default:
        exit('操作命令不支持.');
}