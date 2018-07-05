<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-17
 * Time: 下午10:23
 */
class ImageController extends CommonController {
    public $signStatus = false;

    public function init() {
        parent::init();
        $this->signStatus = false;
    }

    /**
     * 获取百度编辑器配置
     * @api {get} /Index/Image/index 获取百度编辑器配置
     * @apiDescription 获取百度编辑器配置
     * @apiGroup File
     * @apiParam {string} action 动作名称 config:获取配置 uploadimage:上传图片
     * @apiParam {string} [callback] 回调函数名
     * @SyFilter-{"field": "action","explain": "动作名称","type": "string","rules": {"min": 1,"required":1}}
     * @SyFilter-{"field": "callback","explain": "回调函数名","type": "string","rules": {"min": 0}}
     */
    public function indexAction() {
        $action = (string)\Request\SyRequest::getParams('action');
        $handleRes = \Dao\ApiImageDao::indexUeditorHandle($action);
        if(is_string($handleRes)){
            $this->sendRsp($handleRes);
        } else {
            $this->sendRsp(\Tool\Tool::jsonEncode($handleRes));
        }
    }

    public function createQrImageAction() {
        $res = \SyModule\SyModuleService::getInstance()->sendApiReq('/Index/Image/createQrImage', $_GET);
        $this->sendRsp($res);
    }

    public function createCodeImageAction() {
        $data = $_GET;
        $data['session_id'] = \Tool\SySession::getSessionId();
        $res = \SyModule\SyModuleService::getInstance()->sendApiReq('/Index/Image/createCodeImage', $data);
        $this->sendRsp($res);
    }

    /**
     * 上传图片
     * @SyFilter-{"field": "upload_type","explain": "上传类型","type": "int","rules": {"required": 1,"min": 1}}
     * @SyFilter-{"field": "image_base64","explain": "图片base64内容","type": "string","rules": {"baseimage": 1}}
     * @SyFilter-{"field": "image_url","explain": "图片链接","type": "string","rules": {"url": 1}}
     * @SyFilter-{"field": "image_wxmedia","explain": "微信媒体ID","type": "string","rules": {"min": 1}}
     */
    public function uploadImageAction() {
        //思想-不管何种方式的图片上传,都转换成base64编码传递给services服务
        //上传类型 1:文件上传 2:base64上传 3:url上传 4:微信媒体上传
        $uploadType = (int)\Request\SyRequest::getParams('upload_type');
        $handleRes = \Dao\ApiImageDao::uploadImageHandle($uploadType);
        $uploadRes = \SyModule\SyModuleService::getInstance()->sendApiReq('/Index/Image/uploadImage', $handleRes);
        $this->sendRsp($uploadRes);
    }
}