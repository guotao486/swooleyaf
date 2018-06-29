<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-16
 * Time: 2:16
 */
namespace SySms\Yun253;

use DesignPatterns\Singletons\SmsConfigSingleton;

abstract class SmsBase {
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

    public function __construct(){
        $this->account = SmsConfigSingleton::getInstance()->getYun253Config()->getAppKey();
        $this->password = SmsConfigSingleton::getInstance()->getYun253Config()->getAppSecret();
    }

    private function __clone(){
    }

    public function getBaseDetail() : array {
        return [
            'account' => $this->account,
            'password' => $this->password,
        ];
    }

    abstract public function getDetail() : array;
}