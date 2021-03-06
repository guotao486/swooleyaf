<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/1 0001
 * Time: 14:41
 */
namespace AliPay\Life;

use AliPay\AliPayBase;
use Constant\ErrorCode;
use Exception\AliPay\AliPayLifeException;

class GroupDelete extends AliPayBase {
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
     * @throws \Exception\AliPay\AliPayLifeException
     */
    public function setGroupId(string $groupId){
        if(ctype_digit($groupId) && (strlen($groupId) <= 10)){
            $this->biz_content['group_id'] = $groupId;
        } else {
            throw new AliPayLifeException('分组ID不合法', ErrorCode::ALIPAY_LIFE_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(!isset($this->biz_content['group_id'])){
            throw new AliPayLifeException('分组ID不能为空', ErrorCode::ALIPAY_LIFE_PARAM_ERROR);
        }

        return $this->getContent();
    }
}