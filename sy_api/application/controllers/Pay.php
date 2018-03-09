<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-19
 * Time: 下午1:55
 */
class PayController extends CommonController {
    public function init() {
        parent::init();
    }

    /**
     * 发起支付申请
     */
    public function applyPayAction() {
        $allParams = \Request\SyRequest::getParams();
        $allParams['_sytoken'] = \Tool\SySession::getSessionId();
        $applyRes = \SyModule\SyModuleOrder::getInstance()->sendApiReq('/Index/Pay/applyPay', $allParams);
        $this->sendRsp($applyRes);
    }
}