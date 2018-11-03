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

class ShopQueryBatch extends AliBase {
    /**
     * 调用方身份
     * @var string
     */
    private $op_role = '';
    /**
     * 查询类型
     * @var string
     */
    private $query_type = '';
    /**
     * 关联商户PID
     * @var string
     */
    private $related_partner_id = '';
    /**
     * 门店ID
     * @var string
     */
    private $shop_id = '';
    /**
     * 门店状态
     * @var array
     */
    private $shop_status = [];
    /**
     * 页码
     * @var int
     */
    private $page_no = 0;
    /**
     * 每页记录数
     * @var int
     */
    private $page_size = 0;

    public function __construct(string $appId){
        parent::__construct($appId);
        $this->biz_content['page_no'] = 1;
        $this->biz_content['page_size'] = 20;
        $this->setMethod('alipay.offline.market.shop.summary.batchquery');
    }

    private function __clone(){
    }

    /**
     * @param string $opRole
     * @throws \Exception\Ali\AliShopException
     */
    public function setOpRole(string $opRole){
        if(in_array($opRole, ['ISV', 'PROVIDER'])){
            $this->biz_content['op_role'] = $opRole;
        } else {
            throw new AliShopException('调用方身份不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $queryType
     * @throws \Exception\Ali\AliShopException
     */
    public function setQueryType(string $queryType){
        if(in_array($queryType, ['BRAND_RELATION', 'MALL_SELF', 'MALL_RELATION', 'MERCHANT_SELF', 'KB_PROMOTER'])){
            $this->biz_content['query_type'] = $queryType;
        } else {
            throw new AliShopException('查询类型不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $relatedPartnerId
     * @throws \Exception\Ali\AliShopException
     */
    public function setRelatedPartnerId(string $relatedPartnerId){
        if(ctype_digit($relatedPartnerId) && (strlen($relatedPartnerId) <= 16)){
            $this->biz_content['related_partner_id'] = $relatedPartnerId;
        } else {
            throw new AliShopException('关联商户PID不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
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
     * @param string $shopStatus
     * @throws \Exception\Ali\AliShopException
     */
    public function addShopStatus(string $shopStatus){
        if(in_array($shopStatus, ['OPEN', 'PAUSED', 'INIT', 'FREEZE', 'CLOSED'])){
            $this->shop_status[$shopStatus] = 1;
        } else {
            throw new AliShopException('门店状态不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param int $pageNo
     * @throws \Exception\Ali\AliShopException
     */
    public function setPageNo(int $pageNo){
        if($pageNo > 0){
            $this->biz_content['page_no'] = $pageNo;
        } else {
            throw new AliShopException('页码不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param int $pageSize
     * @throws \Exception\Ali\AliShopException
     */
    public function setPageSize(int $pageSize){
        if(($pageSize > 0) && ($pageSize <= 100)){
            $this->biz_content['page_size'] = $pageSize;
        } else {
            throw new AliShopException('每页记录数不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(!isset($this->biz_content['op_role'])){
            throw new AliShopException('调用方身份不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if(!isset($this->biz_content['query_type'])){
            throw new AliShopException('查询类型不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if(!empty($this->shop_status)){
            $this->biz_content['shop_status'] = implode(',', array_keys($this->shop_status));
        }

        return $this->getContent();
    }
}