<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/6 0006
 * Time: 15:20
 */
namespace Ali\Pay;

use Ali\AliBase;
use Constant\ErrorCode;
use Exception\Ali\AliPayException;

class BillDownload extends AliBase {
    private static $billTypes = [
        'trade',
        'signcustomer',
    ];

    /**
     * 账单类型
     * @var string
     */
    private $bill_type = '';
    /**
     * 账单时间：日账单格式为yyyy-MM-dd，月账单格式为yyyy-MM
     * @var string
     */
    private $bill_date = '';

    public function __construct(string $appId) {
        parent::__construct($appId);
        $this->setMethod('alipay.data.dataservice.bill.downloadurl.query');
    }

    private function __clone(){
    }

    /**
     * @param string $billType
     * @throws \Exception\Ali\AliPayException
     */
    public function setBillType(string $billType) {
        if (in_array($billType, self::$billTypes)) {
            $this->setBizContent('bill_type', $billType);
        } else {
            throw new AliPayException('账单类型不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $billDate
     * @throws \Exception\Ali\AliPayException
     */
    public function setBillDate(string $billDate) {
        if (preg_match('/^\d{4}(\-\d{2}){1,2}$/', $billDate) > 0) {
            $this->setBizContent('bill_date', $billDate);
        } else {
            throw new AliPayException('账单时间不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if (!isset($this->biz_content['bill_type'])) {
            throw new AliPayException('账单类型不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if (!isset($this->biz_content['bill_date'])) {
            throw new AliPayException('账单时间不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        return $this->getContent();
    }
}