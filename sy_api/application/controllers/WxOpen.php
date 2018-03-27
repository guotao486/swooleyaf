<?php
class WxOpenController extends CommonController {
    public $signStatus = false;

    public function init() {
        parent::init();
        $this->signStatus = false;
    }

    /**
     * 处理微信服务器消息通知
     */
    public function handleWxNotifyAction() {
        $allParams = \Request\SyRequest::getParams();
        $allParams['wx_xml'] = \Tool\Tool::getArrayVal($GLOBALS, 'HTTP_RAW_POST_DATA', '');
        $handleRes = \SyModule\SyModuleService::getInstance()->sendApiReq('/Index/WxOpen/handleWxNotify', $allParams);
        $resData = \Tool\Tool::jsonDecode($handleRes);
        if (is_array($resData) && isset($resData['code']) && ($resData['code'] == \Constant\ErrorCode::COMMON_SUCCESS)) {
            $this->sendRsp($resData['data']['result']);
        } else {
            $this->sendRsp('fail');
        }
    }

    /**
     * 处理授权者公众号消息
     */
    public function handleAuthorizerNotifyAction() {
        $allParams = \Request\SyRequest::getParams();
        $allParams['wx_xml'] = \Tool\Tool::getArrayVal($GLOBALS, 'HTTP_RAW_POST_DATA', '');
        $handleRes = \SyModule\SyModuleService::getInstance()->sendApiReq('/Index/WxOpen/handleAuthorizerNotify', $allParams);
        $resData = \Tool\Tool::jsonDecode($handleRes);
        if (is_array($resData) && isset($resData['code']) && ($resData['code'] == \Constant\ErrorCode::COMMON_SUCCESS)) {
            $this->sendRsp($resData['data']['result']);
        } else {
            $this->sendRsp('fail');
        }
    }

    /**
     * 获取开放平台授权地址
     * @api {get} /Index/WxOpen/getComponentAuthUrl 获取开放平台授权地址
     * @apiDescription 获取开放平台授权地址
     * @apiGroup ServiceWxOpen
     * @apiUse CommonSuccess
     * @apiUse CommonFail
     */
    public function getComponentAuthUrlAction() {
        $authUrl = \Wx\WxUtilOpen::getAuthUrl();
        if(strlen($authUrl) > 0){
            $this->SyResult->setData([
                'url' => $authUrl,
            ]);
        } else {
            $this->SyResult->setCodeMsg(\Constant\ErrorCode::COMMON_PARAM_ERROR, '获取授权地址失败');
        }

        $this->sendRsp();
    }
}