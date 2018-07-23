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
use MessageQueue\KafkaConsumerConfig;
use MessageQueue\KafkaProducerConfig;
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
        $producerConfigs = Tool::getConfig('kafka.' . SY_ENV . SY_PROJECT . 'producer');
        $consumerConfigs = Tool::getConfig('kafka.' . SY_ENV . SY_PROJECT . 'consumer');
        $brokers = trim(Tool::getArrayVal($consumerConfigs, 'metadata_broker_list', ''));
        if(strlen($brokers) == 0){
            throw new KafkaException('broker不能为空', ErrorCode::KAFKA_CONSUMER_ERROR);
        }

        $kafkaProducerConf = new KafkaProducerConfig();
        $kafkaProducerConf->setRequestRequiredAcks((int)Tool::getArrayVal($producerConfigs, 'request.required.acks', 1, true));
        $kafkaProducerConf->setRequestTimeoutMs((int)Tool::getArrayVal($producerConfigs, 'request.timeout.ms', 3000, true));
        $iniProducerConfigs = $kafkaProducerConf->getDetail();
        $producerConf = new Conf();
        foreach ($iniProducerConfigs as $configKey => $configValue) {
            $producerConf->set($configKey, $configValue);
        }
        $this->producer = new Producer($producerConf);
        $this->producer->setLogLevel(LOG_DEBUG);
        $this->producer->addBrokers($brokers);

        $kafkaConsumerConf = new KafkaConsumerConfig();
        $kafkaConsumerConf->setEnableAutoCommit((int)Tool::getArrayVal($consumerConfigs, 'enable.auto.commit', 1, true));
        $kafkaConsumerConf->setAutoOffsetReset((string)Tool::getArrayVal($consumerConfigs, 'auto.offset.reset', 'smallest', true));
        $kafkaConsumerConf->setGroupId((string)Tool::getArrayVal($consumerConfigs, 'group.id', '', true));
        $kafkaConsumerConf->setMetadataBrokerList((string)Tool::getArrayVal($consumerConfigs, 'metadata.broker.list', '', true));

        $topicConf = new TopicConf();
        $topicConf->set('auto.offset.reset', $kafkaConsumerConf->getAutoOffsetReset());

        $consumerConf = new Conf();
        $consumerConf->set('group.id', $kafkaConsumerConf->getGroupId());
        $consumerConf->set('metadata.broker.list', $brokers);
        $consumerConf->setDefaultTopicConf($topicConf);
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
        $topic->produce(RD_KAFKA_PARTITION_UA, 0, Tool::jsonEncode($data, JSON_UNESCAPED_UNICODE));
        $this->producer->poll(0);

        while ($this->producer->getOutQLen() > 0) {
            $this->producer->poll(50);
        }
    }
}