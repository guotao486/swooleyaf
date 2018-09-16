<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/15 0015
 * Time: 15:34
 */
namespace MessageQueue\Consumer;

use Constant\Project;
use DesignPatterns\Factories\CacheSimpleFactory;
use Log\Log;
use Tool\Tool;

class RedisConsumer {
    /**
     * @var \MessageQueue\Consumer\RedisConsumer
     */
    private static $instance = null;
    /**
     * 管理缓存键名
     * @var string
     */
    private $keyManager = '';
    /**
     * @var int
     */
    private $msgMaxIndex = 0;
    /**
     * 消费者列表
     * @var array
     */
    private $consumers = [];
    /**
     * 主题列表
     * @var array
     */
    private $topics = [];
    /**
     * 延续次数
     * @var int
     */
    private $continueTimes = 0;

    private function __construct(){
        $this->keyManager = Project::REDIS_PREFIX_MESSAGE_QUEUE . 'manager';
        $this->msgMaxIndex = Project::MESSAGE_QUEUE_BATCH_MSG_NUM - 1;
        $this->init();
    }

    private function __clone(){
    }

    /**
     * @return \MessageQueue\Consumer\RedisConsumer
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function init() {
        $this->continueTimes = 0;
        $this->topics = [];
        $this->consumers = [];
        $cacheData = CacheSimpleFactory::getRedisInstance()->hGetAll($this->keyManager);
        if(isset($cacheData['unique_key']) && ($cacheData['unique_key'] == $this->keyManager)){
            unset($cacheData['unique_key']);
            $this->topics = $cacheData;
        }
    }

    /**
     * 获取消费者
     * @param string $topic
     * @return \MessageQueue\Consumer\RedisConsumerService|null
     */
    private function getConsumer(string $topic) {
        if(isset($this->consumers[$topic])){
            return $this->consumers[$topic];
        }
        if(isset($this->topics[$topic])){
            $className = $this->topics[$topic];
            $class = new $className();
            $this->consumers[$topic] = $class;
            return $class;
        }
        return null;
    }

    public function handleData(){
        $this->continueTimes++;
        if($this->continueTimes >= Project::MESSAGE_QUEUE_BATCH_INIT_TIMES){
            $this->init();
        }

        foreach ($this->topics as $topic => $className) {
            $consumer = $this->getConsumer($topic);
            if(is_null($consumer)){
                continue;
            }

            $redisKey = Project::REDIS_PREFIX_MESSAGE_QUEUE . $topic;
            $dataList = CacheSimpleFactory::getRedisInstance()->lRange($redisKey, 0, $this->msgMaxIndex);
            $dataNum = count($dataList);
            if($dataNum > 0){
                CacheSimpleFactory::getRedisInstance()->lTrim($redisKey, $dataNum, -1);
                foreach ($dataList as $eData) {
                    $consumerData = Tool::jsonDecode($eData);
                    if(is_array($consumerData)){
                        try {
                            $consumer->handleMessage($consumerData);
                        } catch (\Exception $e) {
                            Log::error($e->getMessage(), $e->getCode(), $e->getTraceAsString());
                        }
                    } else {
                        Log::error('主题为' . $topic . '的数据消费出错,消费数据为' . $eData);
                    }
                }
            }
        }
    }
}