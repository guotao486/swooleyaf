<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/1 0001
 * Time: 11:00
 */
namespace Ali\Life;

use Ali\AliBase;
use Constant\ErrorCode;
use Exception\Ali\AliPayException;

class TemplateSend extends AliBase {
    /**
     * 用户ID
     * @var string
     */
    private $to_user_id = '';
    /**
     * 模板信息
     * @var array
     */
    private $template = [];

    public function __construct(string $appId){
        parent::__construct($appId);
        $this->setMethod('alipay.open.public.message.single.send');
    }

    public function __clone(){
    }

    /**
     * @param string $userId
     * @throws \Exception\Ali\AliPayException
     */
    public function setToUserId(string $userId){
        if(ctype_digit($userId) && (strlen($userId) <= 32)){
            $this->biz_content['to_user_id'] = $userId;
        } else {
            throw new AliPayException('用户ID不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param array $template
     * @throws \Exception\Ali\AliPayException
     */
    public function setTemplate(array $template){
        if(!empty($template)){
            $this->biz_content['template'] = $template;
        } else {
            throw new AliPayException('模板信息不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if (!isset($this->biz_content['to_user_id'])) {
            throw new AliPayException('用户ID不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if (!isset($this->biz_content['template'])) {
            throw new AliPayException('模板信息不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        return $this->getContent();
    }
}