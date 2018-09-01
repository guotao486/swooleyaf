<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/1 0001
 * Time: 10:31
 */
namespace Wx\Mini;

use Constant\ErrorCode;
use Exception\Wx\WxException;

class MsgTemplateSend extends MiniBase {
    public function __construct(){
        parent::__construct();
    }

    public function __clone(){
    }

    /**
     * 用户openid
     * @var string
     */
    private $openid = '';
    /**
     * 模板ID
     * @var string
     */
    private $templateId = '';
    /**
     * 跳转页面
     * @var string
     */
    private $redirectUrl = '';
    /**
     * 表单ID
     * @var string
     */
    private $formId = '';
    /**
     * 模板内容
     * @var array
     */
    private $data = [];
    /**
     * 放大的关键词
     * @var string
     */
    private $emphasisKeyword = '';

    /**
     * @return string
     */
    public function getOpenid() : string {
        return $this->openid;
    }

    /**
     * @param string $openid
     * @throws \Exception\Wx\WxException
     */
    public function setOpenid(string $openid){
        if (preg_match('/^[0-9a-zA-Z\-\_]{28}$/', $openid) > 0) {
            $this->openid = $openid;
        } else {
            throw new WxException('用户openid不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getTemplateId() : string {
        return $this->templateId;
    }

    /**
     * @param string $templateId
     */
    public function setTemplateId(string $templateId){
        $this->templateId = trim($templateId);
    }

    /**
     * @return string
     */
    public function getRedirectUrl() : string {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     */
    public function setRedirectUrl(string $redirectUrl){
        $this->redirectUrl = trim($redirectUrl);
    }

    /**
     * @return string
     */
    public function getFormId() : string {
        return $this->formId;
    }

    /**
     * @param string $formId
     */
    public function setFormId(string $formId){
        $this->formId = trim($formId);
    }

    /**
     * @return array
     */
    public function getData() : array{
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data){
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getEmphasisKeyword() : string {
        return $this->emphasisKeyword;
    }

    /**
     * @param string $emphasisKeyword
     */
    public function setEmphasisKeyword(string $emphasisKeyword){
        $this->emphasisKeyword = trim($emphasisKeyword);
    }

    public function getDetail() : array {
        if(strlen($this->openid) == 0){
            throw new WxException('用户openid不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if(strlen($this->templateId) == 0){
            throw new WxException('模板ID不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if(strlen($this->formId) == 0){
            throw new WxException('表单ID不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        return [
            'touser' => $this->openid,
            'template_id' => $this->templateId,
            'page' => $this->redirectUrl,
            'form_id' => $this->formId,
            'data' => $this->data,
            'emphasis_keyword' => $this->emphasisKeyword,
        ];
    }
}