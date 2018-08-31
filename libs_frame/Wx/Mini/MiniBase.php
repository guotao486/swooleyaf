<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/8/31 0031
 * Time: 17:27
 */
namespace Wx\Mini;

abstract class MiniBase {
    public function __construct(){
    }

    abstract public function getDetail() : array;
}