<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-16
 * Time: 下午2:28
 */
namespace SyMessageQueue;

abstract class ConsumerBase {
    /**
     * 主题
     * @var string
     */
    public $topic = '';

    public function __construct() {
    }
}