<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/1 0001
 * Time: 14:41
 */
namespace Ali\Life;

use Ali\AliBase;
use Constant\ErrorCode;
use Exception\Ali\AliPayException;

class GroupDelete extends AliBase {
    /**
     * 分组ID
     * @var string
     */
    private $group_id = '';

    public function __construct(string $appId){
        parent::__construct($appId);
        $this->setMethod('alipay.open.public.group.delete');
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

    public function getDetail() : array {
        if(!isset($this->biz_content['group_id'])){
            throw new AliPayException('分组ID不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        return $this->getContent();
    }
}