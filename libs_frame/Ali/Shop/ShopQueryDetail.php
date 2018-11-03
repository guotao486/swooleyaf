<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/2 0002
 * Time: 9:07
 */
namespace Ali\Shop;

use Ali\AliBase;
use Constant\ErrorCode;
use Exception\Ali\AliShopException;

class ShopQueryDetail extends AliBase {
    /**
     * 门店ID
     * @var string
     */
    private $shop_id = '';
    /**
     * 操作人角色
     * @var string
     */
    private $op_role = '';

    public function __construct(string $appId){
        parent::__construct($appId);
        $this->setMethod('alipay.offline.market.shop.querydetail');
    }

    private function __clone(){
    }

    /**
     * @param string $shopId
     * @throws \Exception\Ali\AliShopException
     */
    public function setShopId(string $shopId){
        if(ctype_digit($shopId) && (strlen($shopId) <= 32)){
            $this->biz_content['shop_id'] = $shopId;
        } else {
            throw new AliShopException('门店ID不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $opRole
     * @throws \Exception\Ali\AliShopException
     */
    public function setOpRole(string $opRole){
        if(in_array($opRole, ['MERCHANT', 'PROVIDER'])){
            $this->biz_content['op_role'] = $opRole;
        } else {
            throw new AliShopException('操作人角色不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(!isset($this->biz_content['shop_id'])){
            throw new AliShopException('门店ID不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        return $this->getContent();
    }
}