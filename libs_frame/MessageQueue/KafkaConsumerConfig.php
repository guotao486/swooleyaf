<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/7/23 0023
 * Time: 16:32
 */
namespace MessageQueue;

use Constant\ErrorCode;
use Exception\Kafka\KafkaException;

class KafkaConsumerConfig {
    /**
     * @var bool
     */
    private $enableAutoCommit = true;
    /**
     * @var string
     */
    private $autoOffsetReset = '';
    /**
     * @var string
     */
    private $groupId = '';
    /**
     * @var string
     */
    private $metadataBrokerList = '';

    public function __construct(){
    }

    private function __clone(){
    }

    /**
     * @return bool
     */
    public function isEnableAutoCommit() : bool {
        return $this->enableAutoCommit;
    }

    /**
     * @param int $enableAutoCommit
     */
    public function setEnableAutoCommit(int $enableAutoCommit){
        if($enableAutoCommit > 0){
            $this->enableAutoCommit = true;
        } else {
            $this->enableAutoCommit = false;
        }
    }

    /**
     * @return string
     */
    public function getAutoOffsetReset() : string {
        return $this->autoOffsetReset;
    }

    /**
     * @param string $autoOffsetReset
     * @throws \Exception\Kafka\KafkaException
     */
    public function setAutoOffsetReset(string $autoOffsetReset){
        if(in_array($autoOffsetReset, ['smallest', 'earliest', 'beginning', 'largest', 'latest', 'end', 'error',])) {
            $this->autoOffsetReset = $autoOffsetReset;
        } else {
            throw new KafkaException('配置auto.offset.reset错误', ErrorCode::KAFKA_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getGroupId() : string {
        return $this->groupId;
    }

    /**
     * @param string $groupId
     * @throws \Exception\Kafka\KafkaException
     */
    public function setGroupId(string $groupId){
        if(ctype_alnum($groupId)){
            $this->groupId = $groupId;
        } else {
            throw new KafkaException('配置group.id错误', ErrorCode::KAFKA_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getMetadataBrokerList() : string {
        return $this->metadataBrokerList;
    }

    /**
     * @param string $metadataBrokerList
     * @throws \Exception\Kafka\KafkaException
     */
    public function setMetadataBrokerList(string $metadataBrokerList){
        if(strlen($metadataBrokerList) > 0){
            $this->metadataBrokerList = $metadataBrokerList;
        } else {
            throw new KafkaException('配置metadata.broker.list错误', ErrorCode::KAFKA_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        return [
            'enable.auto.commit' => $this->enableAutoCommit,
            'auto.offset.reset' => $this->autoOffsetReset,
            'group.id' => $this->groupId,
            'metadata.broker.list' => $this->metadataBrokerList,
        ];
    }
}