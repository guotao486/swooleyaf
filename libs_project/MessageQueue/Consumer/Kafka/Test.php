<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/7/23 0023
 * Time: 18:10
 */
namespace MessageQueue\Consumer\Kafka;

use Constant\Project;
use Log\Log;
use MessageQueue\Consumer\KafkaConsumerBase;
use MessageQueue\Consumer\KafkaConsumerService;

class Test extends KafkaConsumerBase implements KafkaConsumerService {
    public function __construct(){
        parent::__construct();
        $this->topic = Project::KAFKA_TOPIC_TEST;
    }

    public function handleMessage(array $data){
        Log::info('kafka msg:' . print_r($data, true));
    }
}