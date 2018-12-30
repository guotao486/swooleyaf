<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/12/30 0030
 * Time: 11:39
 */
namespace SyMessagePush\XinGe;

use Constant\ErrorCode;
use Exception\MessagePush\XinGePushException;
use SyMessagePush\PushBaseXinGe;

class PushApp extends PushBaseXinGe {
    const PLATFORM_TYPE_ALL = 'all';
    const PLATFORM_TYPE_IOS = 'ios';
    const PLATFORM_TYPE_ANDROID = 'android';
    const MESSAGE_TYPE_NOTIFY = 'notify';
    const MESSAGE_TYPE_SILENT = 'message';

    /**
     * 推送目标
     * @var string
     */
    private $audience_type = '';
    /**
     * 平台类型
     * @var string
     */
    private $platform = '';
    /**
     * 消息体
     * @var array
     */
    private $message = [];
    /**
     * 消息类型
     * @var string
     */
    private $message_type = '';
    /**
     * 消息离线存储时间,单位为秒,最长72小时
     * @var int
     */
    private $expire_time = 0;
    /**
     * 推送时间,格式为yyyy-MM-DD HH:MM:SS,若小于服务器当前时间会立即推送,仅全量推送和标签推送支持此字段
     * @var string
     */
    private $send_time = '';
    /**
     * 多包名推送标识,是否推送多个不同渠道包(应用宝、豌豆荚等)
     * @var bool
     */
    private $multi_pkg = false;
    /**
     * 循环任务重复次数,取值范围1-15,支持全推、标签推
     * @var int
     */
    private $loop_times = 0;
    /**
     * 循环执行消息下发间隔,取值范围1-14,以天为单位
     * @var int
     */
    private $loop_interval = 0;
    /**
     * 推送环境,仅限iOS平台推送使用 product:生产环境 dev:开发环境
     * @var string
     */
    private $environment = '';
    /**
     * 角标数字,仅限iOS平台使用,放在aps字段内 -1:角标数字不变 -2:角标数字自动加1 >=0:设置「自定义」角标数字
     * @var int
     */
    private $badge_type = 0;
    /**
     * 统计标签
     * @var string
     */
    private $stat_tag = '';
    /**
     * 请求ID
     * @var int
     */
    private $seq = 0;
    /**
     * 标签列表
     * @var array
     */
    private $tag_list = [];
    /**
     * 账号列表,要求audience_type=account,最多1000个账号
     * @var array
     */
    private $account_list = [];
    /**
     * 单账号推送类型 0:往单个账号的最新的device上推送信息 1:往单个账号关联的所有device设备上推送信息
     * @var int
     */
    private $account_push_type = 0;
    /**
     * 账号类型
     * @var int
     */
    private $account_type = 0;
    /**
     * 设备列表,要求audience_type=token,最多1000个设备
     * @var array
     */
    private $token_list = [];
    /**
     * 推送ID
     * @var int
     */
    private $push_id = 0;

    public function __construct(string $platform){
        parent::__construct($platform);
        $this->apiPath = 'push';
        $this->apiMethod = 'app';
    }

    private function __clone(){
    }

    /**
     * @param string $audienceType
     * @throws \Exception\MessagePush\XinGePushException
     */
    public function setAudienceType(string $audienceType){
        if(in_array($audienceType, ['all', 'tag', 'token', 'token_list', 'account', 'account_list'])){
            $this->reqData['audience_type'] = $audienceType;
        } else {
            throw new XinGePushException('推送目标不合法', ErrorCode::MESSAGE_PUSH_PARAM_ERROR);
        }
    }

    /**
     * @param string $platform
     * @throws \Exception\MessagePush\XinGePushException
     */
    public function setPlatform(string $platform){
        $this->platform = $platform;
        if(in_array($platform, [self::PLATFORM_TYPE_ALL, self::PLATFORM_TYPE_IOS, self::PLATFORM_TYPE_ANDROID])){
            $this->reqData['platform'] = $platform;
        } else {
            throw new XinGePushException('平台类型不合法', ErrorCode::MESSAGE_PUSH_PARAM_ERROR);
        }
    }

    /**
     * @param array $message
     * @throws \Exception\MessagePush\XinGePushException
     */
    public function setMessage(array $message){
        if(empty($message)){
            throw new XinGePushException('消息体不合法', ErrorCode::MESSAGE_PUSH_PARAM_ERROR);
        }

        $this->reqData['message'] = $message;
    }

    /**
     * @param string $messageType
     * @throws \Exception\MessagePush\XinGePushException
     */
    public function setMessageType(string $messageType){
        if(in_array($messageType, [self::MESSAGE_TYPE_NOTIFY, self::MESSAGE_TYPE_SILENT])){
            $this->reqData['message_type'] = $messageType;
        } else {
            throw new XinGePushException('消息类型不合法', ErrorCode::MESSAGE_PUSH_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(!isset($this->reqData['audience_type'])){
            throw new XinGePushException('推送目标不能为空', ErrorCode::MESSAGE_PUSH_PARAM_ERROR);
        }
        if(!isset($this->reqData['platform'])){
            throw new XinGePushException('平台类型不能为空', ErrorCode::MESSAGE_PUSH_PARAM_ERROR);
        }
        if(!isset($this->reqData['message'])){
            throw new XinGePushException('消息体不能为空', ErrorCode::MESSAGE_PUSH_PARAM_ERROR);
        }
        if(!isset($this->reqData['message_type'])){
            throw new XinGePushException('消息类型不能为空', ErrorCode::MESSAGE_PUSH_PARAM_ERROR);
        }

        return $this->getContent();
    }
}