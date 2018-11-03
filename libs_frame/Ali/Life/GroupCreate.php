<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/1 0001
 * Time: 14:40
 */
namespace Ali\Life;

use Ali\AliBase;
use Constant\ErrorCode;
use Exception\Ali\AliLifeException;

class GroupCreate extends AliBase {
    /**
     * 分组名称
     * @var string
     */
    private $name = '';
    /**
     * 标签规则
     * @var array
     */
    private $label_rule = [];

    public function __construct(string $appId){
        parent::__construct($appId);
        $this->biz_content['label_rule'] = [];
        $this->setMethod('alipay.open.public.group.create');
    }

    private function __clone(){
    }

    /**
     * @param string $name
     * @throws \Exception\Ali\AliLifeException
     */
    public function setName(string $name){
        $length = strlen($name);
        if(($length > 0) && ($length <= 30)){
            $this->biz_content['name'] = $name;
        } else {
            throw new AliLifeException('分组名称不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param array $labelRule
     * @throws \Exception\Ali\AliLifeException
     */
    public function addLabelRule(array $labelRule){
        if(!empty($labelRule)){
            $this->biz_content['label_rule'][] = $labelRule;
        } else {
            throw new AliLifeException('标签规则不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(!isset($this->biz_content['name'])){
            throw new AliLifeException('分组名称不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if(count($this->biz_content['label_rule']) == 0){
            throw new AliLifeException('标签规则不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        return $this->getContent();
    }
}