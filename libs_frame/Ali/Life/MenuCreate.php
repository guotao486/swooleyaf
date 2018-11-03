<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/1 0001
 * Time: 16:58
 */
namespace Ali\Life;

use Ali\AliBase;
use Constant\ErrorCode;
use Exception\Ali\AliLifeException;

class MenuCreate extends AliBase {
    /**
     * 菜单类型
     * @var string
     */
    private $type = '';
    /**
     * 菜单列表
     * @var array
     */
    private $button = [];

    public function __construct(string $appId){
        parent::__construct($appId);
        $this->biz_content['type'] = 'text';
        $this->biz_content['button'] = [];
        $this->setMethod('alipay.open.public.menu.create');
    }

    private function __clone(){
    }

    /**
     * @param string $type
     * @throws \Exception\Ali\AliLifeException
     */
    public function setType(string $type){
        if(in_array($type, ['icon', 'text'])){
            $this->biz_content['type'] = $type;
        } else {
            throw new AliLifeException('菜单类型不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param array $button
     * @throws \Exception\Ali\AliLifeException
     */
    public function addButton(array $button){
        if(!empty($button)){
            $this->biz_content['button'][] = $button;
        } else {
            throw new AliLifeException('菜单内容不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(count($this->biz_content['button']) == 0){
            throw new AliLifeException('菜单列表不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        return $this->getContent();
    }
}