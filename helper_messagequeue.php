<?php
require_once __DIR__ . '/helper_load.php';

define('SY_MODULE', SY_PROJECT . 'task');
define('SY_SERVER_IP', (string)\Tool\Tool::getConfig('syserver.base.server.host'));
set_exception_handler('\SyError\ErrorHandler::handleException');
set_error_handler('\SyError\ErrorHandler::handleError');
\Log\Log::setPath(SY_LOG_PATH);

/**
 * 消息队列消费
 */

function syMessageQueueHelp(){
    print_r('帮助信息' . PHP_EOL);
    print_r('-t 消息队列类型: redis kafka' . PHP_EOL);
}

function startRedisConsumer() {
    global $consumer;
    $consumer->start();
}

$type = \Tool\Tool::getClientOption('-t');
switch ($type) {
    case 'redis':
        $consumer = new \MessageQueue\Consumer\RedisConsumer();
        pcntl_signal(SIGALRM, 'startRedisConsumer');

        while (true) {
            pcntl_alarm(1);
            pcntl_signal_dispatch();
            sleep(1);
        }
        break;
    case 'kafka':
        $kafka = new \Helper\MessageQueueKafka();

        while (true) {
            $message = \DesignPatterns\Singletons\KafkaSingleton::getInstance()->getConsumer()->consume(PHP_INT_MAX);
            $kafka->handle($message);
        }
        break;
    default:
        syMessageQueueHelp();
}