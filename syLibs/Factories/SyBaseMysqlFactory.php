<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-3-10
 * Time: 21:47
 */
namespace Factories;

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
use Traits\SimpleTrait;

class SyBaseMysqlFactory {
    use SimpleTrait;

    /**
     * @return \Entities\SyBase\AttachmentBaseEntity
     */
    public static function AttachmentBaseEntity(){
        return new AttachmentBaseEntity();
    }
    /**
     * @return \Entities\SyBase\AttachmentReferEntity
     */
    public static function AttachmentReferEntity(){
        return new AttachmentReferEntity();
    }
    /**
     * @return \Entities\SyBase\LogModuleEntity
     */
    public static function LogModuleEntity(){
        return new LogModuleEntity();
    }
    /**
     * @return \Entities\SyBase\PayHistoryEntity
     */
    public static function PayHistoryEntity(){
        return new PayHistoryEntity();
    }
    /**
     * @return \Entities\SyBase\RefundBaseEntity
     */
    public static function RefundBaseEntity(){
        return new RefundBaseEntity();
    }
    /**
     * @return \Entities\SyBase\RefundHistoryEntity
     */
    public static function RefundHistoryEntity(){
        return new RefundHistoryEntity();
    }
    /**
     * @return \Entities\SyBase\RegionsEntity
     */
    public static function RegionsEntity(){
        return new RegionsEntity();
    }
    /**
     * @return \Entities\SyBase\SmsRecordEntity
     */
    public static function SmsRecordEntity(){
        return new SmsRecordEntity();
    }
    /**
     * @return \Entities\SyBase\TimedTaskEntity
     */
    public static function TimedTaskEntity(){
        return new TimedTaskEntity();
    }
    /**
     * @return \Entities\SyBase\UserBaseEntity
     */
    public static function UserBaseEntity(){
        return new UserBaseEntity();
    }
    /**
     * @return \Entities\SyBase\UserLoginHistoryEntity
     */
    public static function UserLoginHistoryEntity(){
        return new UserLoginHistoryEntity();
    }
    /**
     * @return \Entities\SyBase\UserMoneyEntity
     */
    public static function UserMoneyEntity(){
        return new UserMoneyEntity();
    }
    /**
     * @return \Entities\SyBase\UserMoneyHistoryEntity
     */
    public static function UserMoneyHistoryEntity(){
        return new UserMoneyHistoryEntity();
    }
    /**
     * @return \Entities\SyBase\WithdrawBaseEntity
     */
    public static function WithdrawBaseEntity(){
        return new WithdrawBaseEntity();
    }
    /**
     * @return \Entities\SyBase\WithdrawHistoryEntity
     */
    public static function WithdrawHistoryEntity(){
        return new WithdrawHistoryEntity();
    }
    /**
     * @return \Entities\SyBase\WxopenAuthorizerEntity
     */
    public static function WxopenAuthorizerEntity(){
        return new WxopenAuthorizerEntity();
    }
}