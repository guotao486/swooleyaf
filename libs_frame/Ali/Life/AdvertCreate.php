<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/1 0001
 * Time: 15:00
 */
namespace Ali\Life;

use Ali\AliBase;
use Constant\ErrorCode;
use Exception\Ali\AliPayException;

class AdvertCreate extends AliBase {
    /**
     * 广告内容列表
     * @var array
     */
    private $advert_items = [];

    public function __construct(string $appId){
        parent::__construct($appId);
        $this->biz_content['advert_items'] = [];
        $this->setMethod('alipay.open.public.advert.create');
    }

    private function __clone(){
    }

    /**
     * @param array $advertItem
     * @throws \Exception\Ali\AliPayException
     */
    public function addAdvertItems(array $advertItem){
        if(count($this->biz_content['advert_items']) >= 3){
            throw new AliPayException('广告内容列表超过限制', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if(empty($advertItem)){
            throw new AliPayException('广告内容不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        $this->biz_content['advert_items'][] = $advertItem;
    }

    public function getDetail() : array {
        if(count($this->biz_content['advert_items']) == 0){
            throw new AliPayException('广告内容列表不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        return $this->getContent();
    }
}