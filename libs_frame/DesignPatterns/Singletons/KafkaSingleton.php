<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/7/23 0023
 * Time: 11:59
 */
namespace DesignPatterns\Singletons;

use Constant\ErrorCode;
use Exception\Kafka\KafkaException;
use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Producer;
use RdKafka\TopicConf;
use Tool\Tool;
use Traits\SingletonTrait;

class KafkaSingleton {
    use SingletonTrait;

    /**
     * @var null|\RdKafka\Producer
     */
    private $producer = null;
    /**
     * @var null|\RdKafka\KafkaConsumer
     */
    private $consumer = null;

    private function __construct(){
        $kafkaConfigs = Tool::getConfig('kafka.' . SY_ENV . SY_PROJECT);
        $brokers = trim(Tool::getArrayVal($kafkaConfigs, 'common.metadata.broker.list', '', true));
        if(strlen($brokers) == 0){
            throw new KafkaException('broker不能为空', ErrorCode::KAFKA_CONSUMER_ERROR);
        }

        //设置生产者配置
        $producerConf = new Conf();
        $producerConf->set('request.required.acks', (int)Tool::getArrayVal($kafkaConfigs, 'producer.request.required.acks', 1, true));
        $producerConf->set('request.timeout.ms', (int)Tool::getArrayVal($kafkaConfigs, 'producer.request.timeout.ms', 3000, true));
        $this->producer = new Producer($producerConf);
        $this->producer->setLogLevel(LOG_DEBUG);
        $this->producer->addBrokers($brokers);

        //设置消费者配置
        $groupId = SY_ENV . SY_PROJECT . Tool::createNonceStr(4) . time();
        $consumerTopicConf = new TopicConf();
        $consumerTopicConf->set('auto.offset.reset', (string)Tool::getArrayVal($kafkaConfigs, 'consumer.auto.offset.reset', 'earliest', true));
        $consumerTopicConf->set('offset.store.sync.interval.ms', (int)Tool::getArrayVal($kafkaConfigs, 'consumer.offset.store.sync.interval.ms', 0, true));
        $consumerConf = new Conf();
        $consumerConf->set('group.id', $groupId);
        $consumerConf->set('metadata.broker.list', $brokers);
        $consumerConf->set('enable.auto.commit', true);
        $consumerConf->set('auto.commit.interval.ms', 0);
        $consumerConf->set('enable.auto.offset.store', true);
        $consumerConf->set('offset.store.method', 'broker');
        $consumerConf->set('fetch.wait.max.ms', (int)Tool::getArrayVal($kafkaConfigs, 'consumer.fetch.wait.max.ms', 2000, true));
        $consumerConf->setDefaultTopicConf($consumerTopicConf);
        $consumerConf->setRebalanceCb(function (KafkaConsumer $kafka, $err,array $partitions=null) {
            switch ($err) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                    $kafka->assign($partitions);
                    break;
                case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                    $kafka->assign(null);
                    break;
                default:
                    throw new KafkaException('kafka消费出错', ErrorCode::KAFKA_CONSUMER_ERROR);
            }
        });
        $this->consumer = new KafkaConsumer($consumerConf);
        $this->consumer->subscribe([
            '^' . SY_ENV . SY_PROJECT . '[0-9a-zA-Z]+',
        ]);
    }

    /**
     * @return \DesignPatterns\Singletons\KafkaSingleton
     */
    public static function getInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return null|\RdKafka\KafkaConsumer
     */
    public function getConsumer(){
        return $this->consumer;
    }

    /**
     * 发送kafka数据
     * @param string $topicName 主题名称
     * @param array $data 数据
     */
    public function sendData(string $topicName,array $data) {
        $topic = $this->producer->newTopic($topicName);
        foreach ($data as $eData) {
            $topic->produce(RD_KAFKA_PARTITION_UA, 0, Tool::jsonEncode($eData, JSON_UNESCAPED_UNICODE));
            $this->producer->poll(0);
        }

        while ($this->producer->getOutQLen() > 0) {
            $this->producer->poll(50);
        }
    }
}