<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-9
 * Time: 下午1:05
 */
namespace Map;

abstract class MapBase {
    public function __construct(){
    }

    abstract protected function getContent() : array;
    abstract public function getDetail() : array;
}