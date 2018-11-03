<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/1 0001
 * Time: 14:39
 */
namespace Ali\Life;

use Ali\AliBase;
use Constant\ErrorCode;
use Exception\Ali\AliLifeException;

class MessageSendGroup extends AliBase {
    /**
     * 分组ID
     * @var string
     */
    private $group_id = '';
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
     * 图片消息内容
     * @var array
     */
    private $image = [];

    public function __construct(string $appId){
        parent::__construct($appId);
        $this->setMethod('alipay.open.public.message.group.send');
    }

    private function __clone(){
    }

    /**
     * @param string $groupId
     * @throws \Exception\Ali\AliLifeException
     */
    public function setGroupId(string $groupId){
        if(ctype_digit($groupId) && (strlen($groupId) <= 10)){
            $this->biz_content['group_id'] = $groupId;
        } else {
            throw new AliLifeException('分组ID不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $msgType
     * @throws \Exception\Ali\AliLifeException
     */
    public function setMsgType(string $msgType){
        if(in_array($msgType, ['text', 'image', 'image-text'])){
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
            unset($this->biz_content['text'], $this->biz_content['image']);
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
            unset($this->biz_content['articles'], $this->biz_content['image']);
        } else {
            throw new AliLifeException('文本消息内容不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param array $image
     * @throws \Exception\Ali\AliLifeException
     */
    public function setImage(array $image){
        if(!empty($image)){
            $this->biz_content['image'] = $image;
            unset($this->biz_content['articles'], $this->biz_content['text']);
        } else {
            throw new AliLifeException('图片消息内容不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(!isset($this->biz_content['group_id'])){
            throw new AliLifeException('分组ID不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if(!isset($this->biz_content['msg_type'])){
            throw new AliLifeException('消息类型不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        switch ($this->biz_content['msg_type']) {
            case 'image-text':
                if(!isset($this->biz_content['articles'])){
                    throw new AliLifeException('图文消息内容不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
                }
                break;
            case 'text':
                if(!isset($this->biz_content['text'])){
                    throw new AliLifeException('文本消息内容不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
                }
                break;
            case 'image':
                if(!isset($this->biz_content['image'])){
                    throw new AliLifeException('图片消息内容不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
                }
                break;
        }

        return $this->getContent();
    }
}