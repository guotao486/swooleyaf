<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-9
 * Time: 下午1:05
 */
namespace Map;

abstract class MapBase {
    /**
     * curl配置
     * @var array
     */
    protected $curlConfigs = [];
    /**
     * 请求数据
     * @var array
     */
    protected $reqData = [];

    public function __construct(){
    }

    /**
     * @return array
     */
    public function getCurlConfigs() : array {
        return $this->curlConfigs;
    }

    abstract protected function getContent() : array;
    abstract public function getDetail() : array;
}