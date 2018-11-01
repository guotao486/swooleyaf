<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/1 0001
 * Time: 10:49
 */
namespace Ali\Life;

use Ali\AliBase;
use Constant\ErrorCode;
use Exception\Ali\AliPayException;

class FollowList extends AliBase {
    /**
     * 分组用户ID
     * @var string
     */
    private $next_user_id = '';

    public function __construct(string $appId){
        parent::__construct($appId);
        $this->setMethod('alipay.open.public.follow.batchquery');
    }

    private function __clone(){
    }

    /**
     * @param string $nextUserId
     * @throws \Exception\Ali\AliPayException
     */
    public function setNextUserId(string $nextUserId){
        if(ctype_alnum($nextUserId) && (strlen($nextUserId) <= 32)){
            $this->biz_content['next_user_id'] = $nextUserId;
        } else {
            throw new AliPayException('分组用户ID不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        return $this->getContent();
    }
}