<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-04
 * Time: 0:50
 */
namespace Wx\Shop;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;
use Tool\Tool;
use Wx\WxUtilShop;

class OrderBill extends ShopBase {
    public function __construct(string $appId) {
        parent::__construct();
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->appid = $shopConfig->getAppId();
        $this->mch_id = $shopConfig->getPayMchId();
        $this->sign_type = 'MD5';
        $this->nonce_str = Tool::createNonceStr(32, 'numlower');
        $this->tar_type = 'GZIP';
        $this->bill_type = 'ALL';
    }

    private function __clone(){
    }

    /**
     * 公众号ID
     * @var string
     */
    private $appid = '';

    /**
     * 商户号
     * @var string
     */
    private $mch_id = '';

    /**
     * 设备号
     * @var string
     */
    private $device_info = '';

    /**
     * 随机字符串
     * @var string
     */
    private $nonce_str = '';

    /**
     * 签名类型
     * @var string
     */
    private $sign_type = '';

    /**
     * 对账单日期
     * @var string
     */
    private $bill_date = '';

    /**
     * 账单类型
     * @var string
     */
    private $bill_type = '';

    /**
     * 压缩账单
     * @var string
     */
    private $tar_type = '';

    /**
     * @param string $device_info
     */
    public function setDeviceInfo(string $device_info) {
        $this->device_info = $device_info;
    }

    /**
     * @param string $billDate
     * @throws \Exception\Wx\WxException
     */
    public function setBillDate(string $billDate) {
        if (preg_match('/^[0-9]{8}$/', $billDate) > 0) {
            $this->bill_date = $billDate;
        } else {
            throw new WxException('对账单日期不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $billType
     * @throws \Exception\Wx\WxException
     */
    public function setBillType(string $billType) {
        if (in_array($billType, ['ALL', 'SUCCESS', 'REFUND', 'RECHARGE_REFUND'])) {
            $this->bill_type = $billType;
        } else {
            throw new WxException('账单类型不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(strlen($this->bill_date) == 0){
            throw new WxException('对账单日期不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr = [
            'appid' => $this->appid,
            'mch_id' => $this->mch_id,
            'sign_type' => $this->sign_type,
            'nonce_str' => $this->nonce_str,
            'tar_type' => $this->tar_type,
            'bill_type' => $this->bill_type,
            'bill_date' => $this->bill_date,
        ];
        if(strlen($this->device_info) > 0){
            $resArr['device_info'] = $this->device_info;
        }
        $resArr['sign'] = WxUtilShop::createSign($resArr, $this->appid);

        return $resArr;
    }
}