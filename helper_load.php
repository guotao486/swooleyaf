<?php
require_once __DIR__ . '/syLibs/autoload.php';

ini_set('display_errors', 'On');
error_reporting(E_ALL);
date_default_timezone_set('PRC');
define('SY_VERSION', '3.1');
define('SY_ROOT', __DIR__);
define('SY_ENV', 'dev');
define('SY_PROJECT', 'z01');
define('SY_LOG_PATH', ini_get('seaslog.default_basepath') . 'sy' . SY_PROJECT . '/');