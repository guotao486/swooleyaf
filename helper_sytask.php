<?php
require_once __DIR__ . '/helper_load.php';

function sendSyGetReq(string $url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 2000);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
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
$clearApiSign = $needMinute1 == 1 ? true : false;
$clearLocalUser = $needMinute1 == 2 ? true : false;

$wxTag = \Tool\Tool::getClientOption('-refreshwx');
if($wxTag > 0){
    $wxRefreshMinute = -1;
} else if(($hour % 2) == 0){
    $wxRefreshMinute = $minute;
} else {
    $wxRefreshMinute = $minute + 60;
}

$refreshWx = in_array($wxRefreshMinute, [-1, 33, 73, 113]);
$taskParams = [
    'task_minute' => $minute,
    'task_hour' => $hour,
    'wxcache_refresh' => $refreshWx,
    'clear_apisign' => $clearApiSign,
    'clear_localuser' => $clearLocalUser,
];

if($refreshWx){
    $shopConfigs = \DesignPatterns\Singletons\WxConfigSingleton::getInstance()->getShopConfigs();
    $taskParams['wxcaches'] = [];
    foreach ($shopConfigs as $eAppId => $shopConfig) {
        $wxAccessToken = \Wx\WxUtil::refreshAccessToken($eAppId);
        $wxJsTicket = \Wx\WxUtil::refreshJsTicket($eAppId, $wxAccessToken);
        $taskParams['wxcaches'][] = [
            'appid' => $eAppId,
            'accesstoken' => $wxAccessToken,
            'jsticket' => $wxJsTicket,
        ];
    }
}

foreach ($modules as $moduleTag => $eModule) {
    $taskParams['projects'] = $eModule['projects'];
    $task = $container->getObj($moduleTag);
    $task->handleTask($taskParams);
}

$apiDomain = 'http://api3.xxx.cn';
//发送投票报名通知短信
$url = $apiDomain . '/Index/VoteSmsUser/sendEnterSmsTask';
\Request\RequestSign::makeSignUrl($url);
sendSyGetReq($url);

//检测投票报名通知短信
$url = $apiDomain . '/Index/VoteSmsUser/checkEnterSmsTask';
\Request\RequestSign::makeSignUrl($url);
sendSyGetReq($url);