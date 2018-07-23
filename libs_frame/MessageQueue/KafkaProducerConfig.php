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

class KafkaProducerConfig {
    /**
     * @var int
     */
    private $requestRequiredAcks = 0;
    /**
     * @var int
     */
    private $requestTimeoutMs = 0;

    public function __construct(){
    }

    private function __clone(){
    }

    /**
     * @return int
     */
    public function getRequestRequiredAcks() : int {
        return $this->requestRequiredAcks;
    }

    /**
     * @param int $requestRequiredAcks
     * @throws \Exception\Kafka\KafkaException
     */
    public function setRequestRequiredAcks(int $requestRequiredAcks){
        if(in_array($requestRequiredAcks, [-1, 0, 1])){
            $this->requestRequiredAcks = $requestRequiredAcks;
        } else {
            throw new KafkaException('配置request.required.acks错误', ErrorCode::KAFKA_PARAM_ERROR);
        }
    }

    /**
     * @return int
     */
    public function getRequestTimeoutMs() : int {
        return $this->requestTimeoutMs;
    }

    /**
     * @param int $requestTimeoutMs
     * @throws \Exception\Kafka\KafkaException
     */
    public function setRequestTimeoutMs(int $requestTimeoutMs){
        if(($requestTimeoutMs >= 1) && ($requestTimeoutMs <= 900000)){
            $this->requestTimeoutMs = $requestTimeoutMs;
        } else {
            throw new KafkaException('配置request.timeout.ms错误', ErrorCode::KAFKA_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        return [
            'request.required.acks' => $this->requestRequiredAcks,
            'request.timeout.ms' => $this->requestTimeoutMs,
        ];
    }
}