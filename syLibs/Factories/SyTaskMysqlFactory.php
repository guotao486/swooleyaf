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
     * @return \Entities\SyTask\TaskBaseEntity
     */
    public static function TaskBaseEntity(){
        return new TaskBaseEntity();
    }
    /**
     * @return \Entities\SyTask\TaskLogEntity
     */
    public static function TaskLogEntity(){
        return new TaskLogEntity();
    }
    /**
     * @return \Entities\SyTask\WxopenAuthorizerEntity
     */
    public static function WxopenAuthorizerEntity(){
        return new WxopenAuthorizerEntity();
    }
    /**
     * @return \Entities\SyTask\WxshopConfigEntity
     */
    public static function WxshopConfigEntity(){
        return new WxshopConfigEntity();
    }
    /**
     * @return \Entities\SyTask\AlipayConfigEntity
     */
    public static function AlipayConfigEntity(){
        return new AlipayConfigEntity();
    }
}