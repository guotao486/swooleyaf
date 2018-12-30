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