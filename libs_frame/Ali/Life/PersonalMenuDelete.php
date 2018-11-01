<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/1 0001
 * Time: 17:18
 */
namespace Ali\Life;

use Ali\AliBase;
use Constant\ErrorCode;
use Exception\Ali\AliPayException;

class PersonalMenuDelete extends AliBase {
    /**
     * 菜单key
     * @var string
     */
    private $menu_key = '';

    public function __construct(string $appId){
        parent::__construct($appId);
        $this->setMethod('alipay.open.public.personalized.menu.delete');
    }

    private function __clone(){
    }

    /**
     * @param string $menuKey
     * @throws \Exception\Ali\AliPayException
     */
    public function setMenuKey(string $menuKey){
        if(ctype_alnum($menuKey) && (strlen($menuKey) <= 32)){
            $this->biz_content['menu_key'] = $menuKey;
        } else {
            throw new AliPayException('菜单key不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(!isset($this->biz_content['menu_key'])){
            throw new AliPayException('菜单key不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        return $this->getContent();
    }
}