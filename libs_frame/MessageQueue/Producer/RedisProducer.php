<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/15 0015
 * Time: 14:45
 */
namespace MessageQueue\Producer;

use Constant\ErrorCode;
use Constant\Project;
use DesignPatterns\Factories\CacheSimpleFactory;
use Exception\MessageQueue\MessageQueueException;
use MessageQueue\Consumer\RedisConsumerService;
use Tool\Tool;

class RedisProducer {
    /**
     * @var \MessageQueue\Producer\RedisProducer
     */
    private static $instance = null;
    /**
     * 管理缓存键名
     * @var string
     */
    private $keyManager = '';

    private function __construct(){
        $this->keyManager = Project::REDIS_PREFIX_MESSAGE_QUEUE . 'manager';
    }

    private function __clone(){
    }

    /**
     * @return \MessageQueue\Producer\RedisProducer
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 添加消费者
     * @param \MessageQueue\Consumer\RedisConsumerService $consumer 生产者对象
     * @return bool
     * @throws \Exception\MessageQueue\MessageQueueException
     */
    public function addConsumer(RedisConsumerService $consumer) {
        $cacheData = [
            'unique_key' => $this->keyManager,
        ];
        $cacheData[$consumer->topic] = '\\' .get_class($consumer);
        if(!CacheSimpleFactory::getRedisInstance()->hMset($this->keyManager, $cacheData)){
            throw new MessageQueueException('添加主题失败', ErrorCode::MESSAGE_QUEUE_TOPIC_ERROR);
        }

        return true;
    }

    /**
     * 删除消费者
     * @param \MessageQueue\Consumer\RedisConsumerService $consumer
     * @return int
     */
    public function deleteConsumer(RedisConsumerService $consumer) {
        return CacheSimpleFactory::getRedisInstance()->hDel($this->keyManager, $consumer->topic);
    }

    /**
     * 添加主题数据
     * @param string $topic
     * @param array $data
     */
    public function addTopicData(string $topic,array $data) {
        foreach ($data as $eData) {
            CacheSimpleFactory::getRedisInstance()->rPush(Project::REDIS_PREFIX_MESSAGE_QUEUE . $topic, Tool::jsonEncode($eData, JSON_UNESCAPED_UNICODE));
        }
    }
}