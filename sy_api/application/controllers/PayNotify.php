<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 18-3-9
 * Time: 下午11:06
 */
class PayNotifyController extends CommonController {
    public $signStatus = false;

    public function init() {
        parent::init();
        $this->signStatus = false;
    }

    /**
     * 处理微信支付通知
     */
    public function handleWxPayNotifyAction() {
        $wxMsg = \Tool\Tool::getArrayVal($GLOBALS, 'HTTP_RAW_POST_DATA', '');
        \Log\Log::log('wx notify data:' . $wxMsg);
        $xmlData = \Wx\WxUtil::xmlToArray($wxMsg);
        if (\Wx\WxUtil::checkSign($xmlData, $xmlData['appid'])) {
            $handleRes = \SyModule\SyModuleOrder::getInstance()->sendApiReq('/Index/Pay/handleWxPayNotify', $xmlData);
            $handleData = \Tool\Tool::jsonDecode($handleRes);
            if(is_array($handleData) && isset($handleData['code']) && ($handleData['code'] == 0)){
                $error = '';
            } else {
                $error = '处理失败';
            }
        } else {
            $error = '签名失败';
        }

        $resXml = '<xml>';
        if (strlen($error) > 0) {
            $resXml .= '<return_code>FAIL</return_code><return_msg>' . $error . '</return_msg>';
        } else {
            $resXml .= '<return_code>SUCCESS</return_code>';
        }
        $resXml .= '</xml>';

        $this->sendRsp($resXml);
    }
}