<?php
require_once __DIR__ . '/helper_load.php';

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
if($type == 'redis'){
    $consumer = new \MessageQueue\Consumer\RedisConsumer();
    pcntl_signal(SIGALRM, 'startRedisConsumer');

    while (true) {
        pcntl_alarm(1);
        pcntl_signal_dispatch();
        sleep(1);
    }
} else if($type == 'kafka'){
    $consumerContainer = new \MessageQueue\Consumer\KafkaConsumerContainer();

    while (true) {
        $message = \DesignPatterns\Singletons\KafkaSingleton::getInstance()->getConsumer()->consume(120000);
        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                $msgData = \Tool\Tool::jsonDecode($message->payload);
                $consumer = $consumerContainer->getObj($message->topic_name);
                if(!is_null($consumer)){
                    $consumer->handleMessage($msgData);
                }
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                break;
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                \Log\Log::error('kafka consumer handle time out');
                break;
            default:
                \Log\Log::error($message->errstr(), $message->err);
                break;
        }
    }
} else {
    syMessageQueueHelp();
}