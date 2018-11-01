<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/1 0001
 * Time: 14:42
 */
namespace Ali\Life;

use Ali\AliBase;
use Constant\ErrorCode;
use Exception\Ali\AliPayException;

class GroupModify extends AliBase {
    /**
     * 分组ID
     * @var string
     */
    private $group_id = '';
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
        $this->setMethod('alipay.open.public.group.modify');
    }

    private function __clone(){
    }

    /**
     * @param string $groupId
     * @throws \Exception\Ali\AliPayException
     */
    public function setGroupId(string $groupId){
        if(ctype_digit($groupId) && (strlen($groupId) <= 10)){
            $this->biz_content['group_id'] = $groupId;
        } else {
            throw new AliPayException('分组ID不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $name
     * @throws \Exception\Ali\AliPayException
     */
    public function setName(string $name){
        $length = strlen($name);
        if(($length > 0) && ($length <= 30)){
            $this->biz_content['name'] = $name;
        } else {
            throw new AliPayException('分组名称不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param array $labelRule
     * @throws \Exception\Ali\AliPayException
     */
    public function addLabelRule(array $labelRule){
        if(!empty($labelRule)){
            $this->biz_content['label_rule'][] = $labelRule;
        } else {
            throw new AliPayException('标签规则不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(!isset($this->biz_content['group_id'])){
            throw new AliPayException('分组ID不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if((!isset($this->biz_content['name'])) && empty($this->biz_content['label_rule'])){
            throw new AliPayException('分组名称和标签规则不能都为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        return $this->getContent();
    }
}