<?php
class WxOpenController extends CommonController {
    public function init() {
        parent::init();
    }

    /**
     * 处理微信服务器消息通知
     */
    public function handleWxNotifyAction() {
        $allParams = \Request\SyRequest::getParams();
        $allParams['wx_xml'] = \Tool\Tool::getArrayVal($GLOBALS, 'HTTP_RAW_POST_DATA', '');
        $handleRes = \SyModule\SyModuleService::getInstance()->sendApiReq('/Index/WxOpen/handleWxNotify', $allParams);
        $resData = \Tool\Tool::jsonDecode($handleRes);

        $this->sendRsp($resData['data']);
    }

    /**
     * 处理授权者公众号消息
     */
    public function handleAuthorizerNotifyAction() {
        $allParams = \Request\SyRequest::getParams();
        $allParams['wx_xml'] = \Tool\Tool::getArrayVal($GLOBALS, 'HTTP_RAW_POST_DATA', '');
        $handleRes = \SyModule\SyModuleService::getInstance()->sendApiReq('/Index/WxOpen/handleAuthorizerNotify', $allParams);
        $resData = \Tool\Tool::jsonDecode($handleRes);

        $this->sendRsp($resData['data']);
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
        $authUrl = \Wx\WxOpenUtil::getAuthUrl();
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