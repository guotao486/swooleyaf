<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/8 0008
 * Time: 15:04
 */
namespace Yun253;

use DesignPatterns\Singletons\Yun253Singleton;

abstract class YunBase {
    /**
     * API账号
     * @var string
     */
    private $account = '';
    /**
     * API密码
     * @var string
     */
    private $password = '';
    /**
     * 服务地址
     * @var string
     */
    protected $serviceUrl = '';
    /**
     * 请求数据
     * @var array
     */
    protected $reqData = [];

    public function __construct(){
    }

    private function __clone(){
    }

    /**
     * @return string
     */
    public function getServiceUrl() : string {
        return $this->serviceUrl;
    }

    protected function getContent() {
        $this->reqData['account'] = Yun253Singleton::getInstance()->getCommonConfig()->getAppKey();
        $this->reqData['password'] = Yun253Singleton::getInstance()->getCommonConfig()->getAppSecret();
        return $this->reqData;
    }

    abstract public function getDetail() : array;
}