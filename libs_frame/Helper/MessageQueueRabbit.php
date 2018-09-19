<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-19
 * Time: 下午7:40
 */
namespace Helper;

class MessageQueueRabbit {
    public function __construct(){
        function callback(AMQPEnvelope $message) {
            global $queue;
            if ($message) {
                $body = $message->getBody();
                echo $body . PHP_EOL;
                $queue->ack($message->getDeliveryTag());
            } else {
                echo 'no message' . PHP_EOL;
            }
        }

        //第二种消费方式,非阻塞
        $message = $queue->get();
        if(!empty($message))
        {
            echo $message->getBody();
            $queue->ack($message->getDeliveryTag());    //应答，代表该消息已经消费
        }
    }

    private function __clone(){
    }
}