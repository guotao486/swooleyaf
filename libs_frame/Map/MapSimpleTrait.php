<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/4 0004
 * Time: 16:15
 */
namespace Map;

use Tool\Tool;

trait MapSimpleTrait {
    private function __clone(){
    }

    public function __toString() {
        $vars = array_merge(get_object_vars($this), parent::getConfigs());
        return Tool::jsonEncode($vars, JSON_UNESCAPED_UNICODE);
    }
}