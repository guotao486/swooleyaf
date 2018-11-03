<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/1 0001
 * Time: 11:41
 */
namespace Ali\Life;

use Ali\AliBase;
use Constant\ErrorCode;
use Exception\Ali\AliLifeException;

class MessageSendSingle extends AliBase {
    /**
     * 用户ID
     * @var string
     */
    private $to_user_id = '';
    /**
     * 消息类型,text:文本消息 image-text:图文消息
     * @var string
     */
    private $msg_type = '';
    /**
     * 图文消息内容
     * @var array
     */
    private $articles = [];
    /**
     * 文本消息内容
     * @var array
     */
    private $text = [];
    /**
     * 聊天消息状态 0:非聊天消息,消息显示在生活号主页 1:聊天消息,消息显示在咨询反馈列表页
     * @var string
     */
    private $chat = '';
    /**
     * 事件类型
     * @var string
     */
    private $event_type = '';

    public function __construct(string $appId){
        parent::__construct($appId);
        $this->biz_content['chat'] = '0';
        $this->setMethod('alipay.open.public.message.custom.send');
    }

    private function __clone(){
    }

    /**
     * @param string $userId
     * @throws \Exception\Ali\AliLifeException
     */
    public function setToUserId(string $userId){
        if(ctype_digit($userId) && (strlen($userId) <= 32)){
            $this->biz_content['to_user_id'] = $userId;
        } else {
            throw new AliLifeException('用户ID不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $msgType
     * @throws \Exception\Ali\AliLifeException
     */
    public function setMsgType(string $msgType){
        if(in_array($msgType, ['text', 'image-text'])){
            $this->biz_content['msg_type'] = $msgType;
        } else {
            throw new AliLifeException('消息类型不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param array $articles
     * @throws \Exception\Ali\AliLifeException
     */
    public function setArticles(array $articles){
        if(!empty($articles)){
            $this->biz_content['articles'] = $articles;
            unset($this->biz_content['text']);
        } else {
            throw new AliLifeException('图文消息内容不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param array $text
     * @throws \Exception\Ali\AliLifeException
     */
    public function setText(array $text){
        if(!empty($text)){
            $this->biz_content['text'] = $text;
            unset($this->biz_content['articles']);
        } else {
            throw new AliLifeException('文本消息内容不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $chat
     * @throws \Exception\Ali\AliLifeException
     */
    public function setChat(string $chat){
        if(in_array($chat, ['0', '1'])){
            $this->biz_content['chat'] = $chat;
        } else {
            throw new AliLifeException('聊天消息状态不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $eventType
     * @throws \Exception\Ali\AliLifeException
     */
    public function setEventType(string $eventType){
        if(in_array($eventType, ['follow', 'click', 'enter_ppchat'])){
            $this->biz_content['event_type'] = $eventType;
        } else {
            throw new AliLifeException('事件类型不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(!isset($this->biz_content['to_user_id'])){
            throw new AliLifeException('用户ID不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if(!isset($this->biz_content['msg_type'])){
            throw new AliLifeException('消息类型不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if(($this->biz_content['msg_type'] == 'image-text') && !isset($this->biz_content['articles'])){
            throw new AliLifeException('图文消息内容不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        } else if(($this->biz_content['msg_type'] == 'text') && !isset($this->biz_content['text'])){
            throw new AliLifeException('文本消息内容不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        return $this->getContent();
    }
}