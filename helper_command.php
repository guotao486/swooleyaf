<?php
require_once __DIR__ . '/helper_load.php';

$action = trim(\Tool\Tool::getClientOption('-action'));
switch ($action) {
    case 'refreshserver':
        $activeModules = \Tool\Tool::getProjectModulesByRedis(SY_PROJECT);
        if (!empty($activeModules)) {
            $results = \Tool\Tool::updateProjectModules($activeModules);
            foreach ($results as $eResult) {
                echo $eResult . PHP_EOL;
            }
        }

        break;
    default:
        exit('操作命令不支持.');
}