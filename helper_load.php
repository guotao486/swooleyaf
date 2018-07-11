<?php
require_once __DIR__ . '/helper_autoload.php';

ini_set('display_errors', 'On');
error_reporting(E_ALL);
date_default_timezone_set('PRC');
define('SY_VERSION', '4.0');
define('SY_ROOT', __DIR__);
define('SY_ENV', 'dev');
define('SY_PROJECT', 'z01');
define('SY_RECONNECT_DB', true);
define('SY_CACHE_WXSHOP', false);
define('SY_CACHE_WXOPEN', false);
$syLogPath = ini_get('seaslog.default_basepath');
if(substr($syLogPath, -1) == '/'){
    $syLogPath .= 'sy' . SY_PROJECT;
} else {
    $syLogPath .= '/sy' . SY_PROJECT;
}
define('SY_LOG_PATH', $syLogPath);
unset($syLogPath);

define('SY_PROJECT_LIBS_ROOT', __DIR__ . '/libs_project/');
$frameLibsDir = \Yaconf::get('project.' . SY_ENV . SY_PROJECT . '.dir.libs.frame');
if(substr($frameLibsDir, -1) == '/'){
    define('SY_FRAME_LIBS_ROOT', $frameLibsDir);
} else {
    define('SY_FRAME_LIBS_ROOT', $frameLibsDir . '/');
}
unset($frameLibsDir);