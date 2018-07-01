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

$projects = include(__DIR__ . '/helper_projects.php');
$container = new \SyTask\SyModuleTaskContainer();

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
foreach ($projects as $eProject) {
    $taskParams['projects'] = $eProject['listens'];
    $task = $container->getObj($eProject['module_name']);
    $task->handleTask($taskParams);
}