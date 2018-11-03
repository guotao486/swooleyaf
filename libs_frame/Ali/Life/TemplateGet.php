<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/1 0001
 * Time: 10:55
 */
namespace Ali\Life;

use Ali\AliBase;
use Constant\ErrorCode;
use Exception\Ali\AliLifeException;

class TemplateGet extends AliBase {
    /**
     * 模板ID
     * @var string
     */
    private $template_id = '';

    public function __construct(string $appId){
        parent::__construct($appId);
        $this->setMethod('alipay.open.public.template.message.get');
    }

    private function __clone(){
    }

    /**
     * @param string $templateId
     * @throws \Exception\Ali\AliLifeException
     */
    public function setTemplateId(string $templateId){
        if(ctype_alnum($templateId) && (strlen($templateId) <= 20)){
            $this->biz_content['template_id'] = $templateId;
        } else {
            throw new AliLifeException('模板ID不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if (!isset($this->biz_content['template_id'])) {
            throw new AliLifeException('模板ID不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        return $this->getContent();
    }
}