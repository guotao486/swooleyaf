<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/15 0015
 * Time: 16:02
 */
namespace Helper;

use MessageQueue\Consumer\RedisConsumerService;
use MessageQueue\Producer\RedisProducer;
use Tool\Tool;

class MessageQueueRedis {
    public function __construct(){
    }

    private function __clone(){
    }

    private function help() {
        print_r('redis帮助信息' . PHP_EOL);
        print_r('-action 操作类型类型: add delete' . PHP_EOL);
        print_r('-classname 消费者类名,从\\开始' . PHP_EOL);
    }

    public function handleOption(){
        $action = Tool::getClientOption('-action', false, '');
        $className = Tool::getClientOption('-classname', false, '');
        switch ($action) {
            case 'add':
                $class = new $className();
                if($class instanceof RedisConsumerService){
                    RedisProducer::getInstance()->addConsumer($class);
                } else {
                    exit('类名不合法');
                }
                break;
            case 'delete':
                $class = new $className();
                if($class instanceof RedisConsumerService){
                    RedisProducer::getInstance()->deleteConsumer($class);
                } else {
                    exit('类名不合法');
                }
                break;
            default:
                $this->help();
        }
    }
}