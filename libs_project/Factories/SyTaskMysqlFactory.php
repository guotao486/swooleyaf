<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-3-10
 * Time: 21:47
 */
namespace Factories;

use Entities\SyTask\AlipayConfigEntity;
use Entities\SyTask\TaskBaseEntity;
use Entities\SyTask\TaskLogEntity;
use Entities\SyTask\WxopenAuthorizerEntity;
use Entities\SyTask\WxshopConfigEntity;
use Traits\SimpleTrait;

class SyTaskMysqlFactory {
    use SimpleTrait;

    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyTask\TaskBaseEntity
     */
    public static function TaskBaseEntity(string $dbName=''){
        return new TaskBaseEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyTask\TaskLogEntity
     */
    public static function TaskLogEntity(string $dbName=''){
        return new TaskLogEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyTask\WxopenAuthorizerEntity
     */
    public static function WxopenAuthorizerEntity(string $dbName=''){
        return new WxopenAuthorizerEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyTask\WxshopConfigEntity
     */
    public static function WxshopConfigEntity(string $dbName=''){
        return new WxshopConfigEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyTask\AlipayConfigEntity
     */
    public static function AlipayConfigEntity(string $dbName=''){
        return new AlipayConfigEntity($dbName);
    }
}