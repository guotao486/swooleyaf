<?php
function controllerLog(string $serverPath,string $command) {
    $phpCommand = 'sudo /usr/local/php7/bin/php helper_service.php -n ' . $serverPath . ' ' . $command;
    system($phpCommand);
}

function getClientOption(string $key, $default=null) {
    global $argv;
    $option = null;
    foreach ($argv as $eKey => $eVal) {
        if(($key == $eVal) && isset($argv[$eKey+1])){
            $option = $argv[$eKey+1];
            break;
        }
    }

    return $option ?? $default;
}

$projects = include(__DIR__ . '/helper_projects.php');

$command = getClientOption('-s');
switch ($command) {
    case 'start-all' :
        foreach ($projects as $eProject) {
            foreach ($eProject['listens'] as $eListen) {
                controllerLog($eProject['module_path'], '-s start -module ' . $eProject['module_name'] . ' -port ' . $eListen['port']);
            }
        }
        break;
    case 'stop-all' :
        foreach ($projects as $eProject) {
            foreach ($eProject['listens'] as $eListen) {
                controllerLog($eProject['module_path'], '-s stop -module ' . $eProject['module_name'] . ' -port ' . $eListen['port']);
            }
        }
        break;
    case 'restart-all' :
        foreach ($projects as $eProject) {
            foreach ($eProject['listens'] as $eListen) {
                controllerLog($eProject['module_path'], '-s restart -module ' . $eProject['module_name'] . ' -port ' . $eListen['port']);
            }
        }
        break;
    case 'kz-all' :
        foreach ($projects as $eProject) {
            foreach ($eProject['listens'] as $eListen) {
                controllerLog($eProject['module_path'], '-s kz -module ' . $eProject['module_name'] . ' -port ' . $eListen['port']);
            }
        }
        break;
    default :
        system('echo -e "\e[1;31m command not exist \e[0m"');
}