<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/1 0001
 * Time: 14:37
 */
namespace Ali\Life;

use Ali\AliBase;

class LifeDebark extends AliBase {
    public function __construct(string $appId){
        parent::__construct($appId);
        $this->setMethod('alipay.open.public.life.debark.apply');
    }

    private function __clone(){
    }

    public function getDetail() : array {
        return $this->getContent();
    }
}