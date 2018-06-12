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
use Entities\SyBase\LogModuleEntity;
use Entities\SyBase\PayHistoryEntity;
use Entities\SyBase\RefundBaseEntity;
use Entities\SyBase\RefundHistoryEntity;
use Entities\SyBase\RegionsEntity;
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
     * @return \Entities\SyBase\AttachmentBaseEntity
     */
    public static function AttachmentBaseEntity(string $dbName=''){
        return new AttachmentBaseEntity($dbName);
    }
    /**
     * @return \Entities\SyBase\AttachmentReferEntity
     */
    public static function AttachmentReferEntity(string $dbName=''){
        return new AttachmentReferEntity($dbName);
    }
    /**
     * @return \Entities\SyBase\LogModuleEntity
     */
    public static function LogModuleEntity(string $dbName=''){
        return new LogModuleEntity($dbName);
    }
    /**
     * @return \Entities\SyBase\PayHistoryEntity
     */
    public static function PayHistoryEntity(string $dbName=''){
        return new PayHistoryEntity($dbName);
    }
    /**
     * @return \Entities\SyBase\RefundBaseEntity
     */
    public static function RefundBaseEntity(string $dbName=''){
        return new RefundBaseEntity($dbName);
    }
    /**
     * @return \Entities\SyBase\RefundHistoryEntity
     */
    public static function RefundHistoryEntity(string $dbName=''){
        return new RefundHistoryEntity($dbName);
    }
    /**
     * @return \Entities\SyBase\RegionsEntity
     */
    public static function RegionsEntity(string $dbName=''){
        return new RegionsEntity($dbName);
    }
    /**
     * @return \Entities\SyBase\SmsRecordEntity
     */
    public static function SmsRecordEntity(string $dbName=''){
        return new SmsRecordEntity($dbName);
    }
    /**
     * @return \Entities\SyBase\TimedTaskEntity
     */
    public static function TimedTaskEntity(string $dbName=''){
        return new TimedTaskEntity($dbName);
    }
    /**
     * @return \Entities\SyBase\UserBaseEntity
     */
    public static function UserBaseEntity(string $dbName=''){
        return new UserBaseEntity($dbName);
    }
    /**
     * @return \Entities\SyBase\UserLoginHistoryEntity
     */
    public static function UserLoginHistoryEntity(string $dbName=''){
        return new UserLoginHistoryEntity($dbName);
    }
    /**
     * @return \Entities\SyBase\UserMoneyEntity
     */
    public static function UserMoneyEntity(string $dbName=''){
        return new UserMoneyEntity($dbName);
    }
    /**
     * @return \Entities\SyBase\UserMoneyHistoryEntity
     */
    public static function UserMoneyHistoryEntity(string $dbName=''){
        return new UserMoneyHistoryEntity($dbName);
    }
    /**
     * @return \Entities\SyBase\WithdrawBaseEntity
     */
    public static function WithdrawBaseEntity(string $dbName=''){
        return new WithdrawBaseEntity($dbName);
    }
    /**
     * @return \Entities\SyBase\WithdrawHistoryEntity
     */
    public static function WithdrawHistoryEntity(string $dbName=''){
        return new WithdrawHistoryEntity($dbName);
    }
    /**
     * @return \Entities\SyBase\WxopenAuthorizerEntity
     */
    public static function WxopenAuthorizerEntity(string $dbName=''){
        return new WxopenAuthorizerEntity($dbName);
    }
    /**
     * @return \Entities\SyBase\WxshopConfigEntity
     */
    public static function WxshopConfigEntity(string $dbName=''){
        return new WxshopConfigEntity($dbName);
    }
    /**
     * @return \Entities\SyBase\AlipayConfigEntity
     */
    public static function AlipayConfigEntity(string $dbName=''){
        return new AlipayConfigEntity($dbName);
    }
}