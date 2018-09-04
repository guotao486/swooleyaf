<?php
/**
 * 扫码支付模式一返回微信数据类
 * User: Administrator
 * Date: 2017-04-03
 * Time: 2:53
 */
namespace Wx\Shop;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;
use Wx\WxUtilShop;

class NativeReturn extends ShopBase {
    public function __construct(string $appId) {
        parent::__construct();
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->appid = $shopConfig->getAppId();
        $this->mch_id = $shopConfig->getPayMchId();
        $this->result_code = 'SUCCESS';
        $this->return_code = 'SUCCESS';
    }

    private function __clone(){
    }

    /**
     * 返回状态码
     * @var string
     */
    private $return_code = '';

    /**
     * 返回信息
     * @var string
     */
    private $return_msg = '';

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
     * 微信返回的随机字符串
     * @var string
     */
    private $nonce_str = '';

    /**
     * 预支付ID
     * @var string
     */
    private $prepay_id = '';

    /**
     * 业务结果
     * @var string
     */
    private $result_code = '';

    /**
     * 错误描述
     * @var string
     */
    private $err_code_des = '';

    /**
     * @param string $nonceStr
     */
    public function setNonceStr(string $nonceStr) {
        $this->nonce_str = $nonceStr;
    }

    /**
     * @param string $prepayId
     */
    public function setPrepayId(string $prepayId) {
        $this->prepay_id = $prepayId;
    }

    /**
     * @param string $errDes 返回给用户的错误描述
     * @param string $returnMsg 返回微信的信息
     * @throws \Exception\Wx\WxException
     */
    public function setErrorMsg(string $errDes,string $returnMsg) {
        if (strlen($errDes) == 0) {
            throw new WxException('错误描述不能为空', ErrorCode::WX_PARAM_ERROR);
        } else if (strlen($returnMsg) == 0) {
            throw new WxException('返回信息不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $this->return_code = 'FAIL';
        $this->return_msg = mb_substr($returnMsg, 0, 40);
        $this->result_code = 'FAIL';
        $this->err_code_des = mb_substr($errDes, 0, 40);
    }

    public function getDetail() : array {
        if($this->return_code == 'SUCCESS'){
            if(strlen($this->nonce_str) == 0){
                throw new WxException('随机字符串不能为空', ErrorCode::WX_PARAM_ERROR);
            } else if(strlen($this->prepay_id) == 0){
                throw new WxException('预支付ID不能为空', ErrorCode::WX_PARAM_ERROR);
            }
        }

        $resArr = [
            'return_code' => $this->return_code,
            'result_code' => $this->result_code,
            'appid' => $this->appid,
            'mch_id' => $this->mch_id,
        ];
        if(strlen($this->nonce_str) > 0){
            $resArr['nonce_str'] = $this->nonce_str;
        }
        if(strlen($this->prepay_id) > 0){
            $resArr['prepay_id'] = $this->prepay_id;
        }
        if(strlen($this->return_msg) > 0){
            $resArr['return_msg'] = $this->return_msg;
        }
        if(strlen($this->err_code_des) > 0){
            $resArr['err_code_des'] = $this->err_code_des;
        }
        $resArr['sign'] = WxUtilShop::createSign($resArr, $this->appid);

        return $resArr;
    }
}