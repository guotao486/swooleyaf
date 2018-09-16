<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-16
 * Time: 下午2:31
 */
namespace SyMessageQueue;

interface ConsumerInterface {
    /**
     * 处理消息
     * @param array $data
     * @return mixed
     */
    public function handleMessage(array $data);
}