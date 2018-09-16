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
use SyMessageQueue\ConsumerBase;
use SyMessageQueue\ConsumerInterface;

class Test extends ConsumerBase implements ConsumerInterface {
    public function __construct(){
        parent::__construct();
        $this->topic = Project::KAFKA_TOPIC_TEST;
    }

    public function handleMessage(array $data){
        Log::info('kafka msg:' . print_r($data, true));
    }
}