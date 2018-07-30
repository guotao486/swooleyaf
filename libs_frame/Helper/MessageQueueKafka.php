<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/7/30 0030
 * Time: 17:24
 */
namespace Helper;

use DesignPatterns\Singletons\KafkaSingleton;
use Log\Log;
use MessageQueue\Consumer\KafkaConsumerContainer;
use RdKafka\Message;
use Tool\Tool;

class MessageQueueKafka {
    /**
     * @var \MessageQueue\Consumer\KafkaConsumerContainer
     */
    private $consumerContainer = null;

    public function __construct(){
        $this->consumerContainer = new KafkaConsumerContainer();
    }

    private function __clone(){
    }

    public function handle(Message $message){
        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                $msgData = Tool::jsonDecode($message->payload);
                $consumer = $this->consumerContainer->getObj($message->topic_name);
                if(!is_null($consumer)){
                    try {
                        $consumer->handleMessage($msgData);
                    } catch (\Exception $e) {
                        Log::error($e->getMessage(), $e->getCode(), $e->getTraceAsString());
                    } finally {
                        unset($consumer);
                        KafkaSingleton::getInstance()->getConsumer()->commit($message);
                    }
                }
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                sleep(3);
                break;
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                Log::error('kafka consumer handle time out');
                break;
            default:
                Log::error($message->errstr(), $message->err);
                break;
        }
    }
}