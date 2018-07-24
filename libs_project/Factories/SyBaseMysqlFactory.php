<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-3-10
 * Time: 21:47
 */
namespace Factories;

use Entities\SyBase\AlipayConfigEntity;
use Entities\SyBase\AttachmentBaseEntity;
use Entities\SyBase\AttachmentReferEntity;
use Entities\SyBase\ImBaseEntity;
use Entities\SyBase\LogModuleEntity;
use Entities\SyBase\PayHistoryEntity;
use Entities\SyBase\RefundBaseEntity;
use Entities\SyBase\RefundHistoryEntity;
use Entities\SyBase\RegionBaseEntity;
use Entities\SyBase\RegionsEntity;
use Entities\SyBase\RoleBaseEntity;
use Entities\SyBase\RolePowerEntity;
use Entities\SyBase\RoleRelationEntity;
use Entities\SyBase\SmsRecordEntity;
use Entities\SyBase\TimedTaskEntity;
use Entities\SyBase\UserBaseEntity;
use Entities\SyBase\UserLoginHistoryEntity;
use Entities\SyBase\UserMoneyEntity;
use Entities\SyBase\UserMoneyHistoryEntity;
use Entities\SyBase\WithdrawBaseEntity;
use Entities\SyBase\WithdrawHistoryEntity;
use Entities\SyBase\WxopenAuthorizerEntity;
use Entities\SyBase\WxshopConfigEntity;
use Traits\SimpleTrait;

class SyBaseMysqlFactory {
    use SimpleTrait;

    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\AttachmentBaseEntity
     */
    public static function AttachmentBaseEntity(string $dbName=''){
        return new AttachmentBaseEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\AttachmentReferEntity
     */
    public static function AttachmentReferEntity(string $dbName=''){
        return new AttachmentReferEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\ImBaseEntity
     */
    public static function ImBaseEntity(string $dbName=''){
        return new ImBaseEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\LogModuleEntity
     */
    public static function LogModuleEntity(string $dbName=''){
        return new LogModuleEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\PayHistoryEntity
     */
    public static function PayHistoryEntity(string $dbName=''){
        return new PayHistoryEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\RefundBaseEntity
     */
    public static function RefundBaseEntity(string $dbName=''){
        return new RefundBaseEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\RefundHistoryEntity
     */
    public static function RefundHistoryEntity(string $dbName=''){
        return new RefundHistoryEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\RegionsEntity
     */
    public static function RegionsEntity(string $dbName=''){
        return new RegionsEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\SmsRecordEntity
     */
    public static function SmsRecordEntity(string $dbName=''){
        return new SmsRecordEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\TimedTaskEntity
     */
    public static function TimedTaskEntity(string $dbName=''){
        return new TimedTaskEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\UserBaseEntity
     */
    public static function UserBaseEntity(string $dbName=''){
        return new UserBaseEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\UserLoginHistoryEntity
     */
    public static function UserLoginHistoryEntity(string $dbName=''){
        return new UserLoginHistoryEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\UserMoneyEntity
     */
    public static function UserMoneyEntity(string $dbName=''){
        return new UserMoneyEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\UserMoneyHistoryEntity
     */
    public static function UserMoneyHistoryEntity(string $dbName=''){
        return new UserMoneyHistoryEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\WithdrawBaseEntity
     */
    public static function WithdrawBaseEntity(string $dbName=''){
        return new WithdrawBaseEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\WithdrawHistoryEntity
     */
    public static function WithdrawHistoryEntity(string $dbName=''){
        return new WithdrawHistoryEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\WxopenAuthorizerEntity
     */
    public static function WxopenAuthorizerEntity(string $dbName=''){
        return new WxopenAuthorizerEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\WxshopConfigEntity
     */
    public static function WxshopConfigEntity(string $dbName=''){
        return new WxshopConfigEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\AlipayConfigEntity
     */
    public static function AlipayConfigEntity(string $dbName=''){
        return new AlipayConfigEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\RegionBaseEntity
     */
    public static function RegionBaseEntity(string $dbName=''){
        return new RegionBaseEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\RoleBaseEntity
     */
    public static function RoleBaseEntity(string $dbName=''){
        return new RoleBaseEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\RolePowerEntity
     */
    public static function RolePowerEntity(string $dbName=''){
        return new RolePowerEntity($dbName);
    }
    /**
     * @param string $dbName 数据库名
     * @return \Entities\SyBase\RoleRelationEntity
     */
    public static function RoleRelationEntity(string $dbName=''){
        return new RoleRelationEntity($dbName);
    }
}