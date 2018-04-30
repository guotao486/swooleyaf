<?php
require_once __DIR__ . '/helper_load.php';

function sendSyGetReq(string $url) {
    \Tool\Tool::sendCurlReq([
        CURLOPT_URL => $url,
        CURLOPT_TIMEOUT_MS => 2000,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
    ]);
}

$container = new \SyTask\SyModuleTaskContainer();
$modules = [
    \Constant\Server::MODULE_NAME_API => [
        'projects' => [
            0 => [
                'host' => '127.0.0.1',
                'port' => 7100,
            ],
        ],
    ],
    \Constant\Server::MODULE_NAME_ORDER => [
        'projects' => [
            0 => [
                'host' => '127.0.0.1',
                'port' => 7120,
            ],
        ],
    ],
    \Constant\Server::MODULE_NAME_USER => [
        'projects' => [
            0 => [
                'host' => '127.0.0.1',
                'port' => 7140,
            ],
        ],
    ],
    \Constant\Server::MODULE_NAME_SERVICE => [
        'projects' => [
            0 => [
                'host' => '127.0.0.1',
                'port' => 7160,
            ],
        ],
    ],
];

$timeArr = explode('-', date('H-i'));
$minute = (int)$timeArr[1];
$hour = (int)$timeArr[0];

$needMinute1 = $minute % 5;

$taskParams = [
    'task_minute' => $minute,
    'task_hour' => $hour,
    'clear_apisign' => $needMinute1 == 1 ? true : false,
    'clear_localuser' => $needMinute1 == 2 ? true : false,
    'clear_localwxshoptoken' => $needMinute1 == 0 ? true : false,
    'clear_localwxopenauthorizertoken' => $needMinute1 == 0 ? true : false,
];

foreach ($modules as $moduleTag => $eModule) {
    $taskParams['projects'] = $eModule['projects'];
    $task = $container->getObj($moduleTag);
    $task->handleTask($taskParams);
}