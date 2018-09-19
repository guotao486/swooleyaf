<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/18 0018
 * Time: 18:32
 */
namespace SyMessageQueue\Rabbit;

class Consumer {
    public function __construct(){
        $params = array(
            'exchangeName' => 'myexchange',
            'queueName' => 'myqueue',
            'routeKey' => 'myroute',
        );

        $connectConfig = array(
            'host' => 'localhost',
            'port' => 5672,
            'login' => 'rabbitmq',
            'password' => 'rabbitmq',
            'vhost' => '/'
        );

        try {
            $conn = new AMQPConnection($connectConfig);
            $conn->connect();
            if (!$conn->isConnected()) {
                //die('Conexiune esuata');
                //TODO 记录日志
                echo 'rabbit-mq 连接错误:', json_encode($connectConfig);
                exit();
            }
            $channel = new AMQPChannel($conn);
            if (!$channel->isConnected()) {
                // die('Connection through channel failed');
                //TODO 记录日志
                echo 'rabbit-mq Connection through channel failed:', json_encode($connectConfig);
                exit();
            }
            $exchange = new AMQPExchange($channel);
            $exchange->setFlags(AMQP_PASSIVE);//声明一个已存在的交换器的，如果不存在将抛出异常，这个一般用在consume端
            $exchange->setName($params['exchangeName']?:'');
            $exchange->setType(AMQP_EX_TYPE_DIRECT); //direct类型
            $exchange->declareExchange();

            //$channel->startTransaction();

            $queue = new AMQPQueue($channel);
            $queue->setName($params['queueName']?:'');
            $queue->setFlags(AMQP_DURABLE);
            $queue->declareQueue();

            //绑定
            $queue->bind($params['exchangeName'], $params['routeKey']);
        } catch(Exception $e) {
            echo $e->getMessage();
            exit();
        }

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

        //$queue->consume('callback');  第一种消费方式,但是会阻塞,程序一直会卡在此处

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