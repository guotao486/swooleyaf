<?php
require_once __DIR__ . '/syLibs/autoload.php';

ini_set('display_errors', 'On');
error_reporting(E_ALL);
date_default_timezone_set('PRC');
define('SY_VERSION', '3.1');
define('SY_ROOT', __DIR__);
define('SY_ENV', 'dev');
define('SY_PROJECT', 'a01');
$syLogPath = ini_get('seaslog.default_basepath');
if(substr($syLogPath, -1) == '/'){
    $syLogPath .= 'sy' . SY_PROJECT;
} else {
    $syLogPath .= '/sy' . SY_PROJECT;
}
define('SY_LOG_PATH', $syLogPath);
unset($syLogPath);