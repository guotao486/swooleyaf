<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-3-10
 * Time: 21:47
 */
namespace Factories;

use Entities\SyTask\AliconfigPayEntity;
use Entities\SyTask\TaskBaseEntity;
use Entities\SyTask\TaskLogEntity;
use Entities\SyTask\WxconfigBaseEntity;
use Entities\SyTask\WxconfigShopEntity;
use Entities\SyTask\WxopenAuthorizerEntity;
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
     * @return \Entities\SyTask\WxconfigBaseEntity
     */
    public static function WxconfigBaseEntity(string $dbName=''){
        return new WxconfigBaseEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyTask\WxconfigShopEntity
     */
    public static function WxconfigShopEntity(string $dbName=''){
        return new WxconfigShopEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyTask\AliconfigPayEntity
     */
    public static function AliconfigPayEntity(string $dbName=''){
        return new AliconfigPayEntity($dbName);
    }
}