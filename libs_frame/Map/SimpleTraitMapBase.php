<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/4 0004
 * Time: 16:27
 */
namespace Map;

trait SimpleTraitMapBase {
    private function __clone() {
    }

    protected function getConfigs() : array {
        return get_object_vars($this);
    }
}