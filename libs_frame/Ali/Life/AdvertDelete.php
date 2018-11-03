<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/1 0001
 * Time: 15:01
 */
namespace Ali\Life;

use Ali\AliBase;
use Constant\ErrorCode;
use Exception\Ali\AliLifeException;

class AdvertDelete extends AliBase {
    /**
     * 广告ID
     * @var string
     */
    private $advert_id = '';

    public function __construct(string $appId){
        parent::__construct($appId);
        $this->setMethod('alipay.open.public.advert.delete');
    }

    private function __clone(){
    }

    /**
     * @param string $advertId
     * @throws \Exception\Ali\AliLifeException
     */
    public function setAdvertId(string $advertId){
        if(ctype_alnum($advertId) && (strlen($advertId) <= 20)){
            $this->biz_content['advert_id'] = $advertId;
        } else {
            throw new AliLifeException('广告ID不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(!isset($this->biz_content['advert_id'])){
            throw new AliLifeException('广告ID不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        return $this->getContent();
    }
}