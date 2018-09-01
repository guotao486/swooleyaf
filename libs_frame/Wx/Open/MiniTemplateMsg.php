<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/7/13 0013
 * Time: 10:07
 */
namespace Wx\Open;

use Constant\ErrorCode;
use Exception\Wx\WxOpenException;

class MiniTemplateMsg extends OpenBase {
    public function __construct(){
        parent::__construct();
    }

    private function __clone(){
    }

    /**
     * 模板ID
     * @var string
     */
    private $templateId = '';
    /**
     * 模板内容
     * @var array
     */
    private $templateContent = [];
    /**
     * 模板关键词
     * @var string
     */
    private $templateKeyword = '';
    /**
     * 用户openid
     * @var string
     */
    private $openid = '';
    /**
     * 跳转链接
     * @var string
     */
    private $redirectUrl = '';

    /**
     * 表单ID
     * @var string
     */
    private $formId = '';

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
        $this->templateId = $templateId;
    }

    /**
     * @return array
     */
    public function getTemplateContent() : array {
        return $this->templateContent;
    }

    /**
     * @param array $templateContent
     */
    public function setTemplateContent(array $templateContent){
        $this->templateContent = $templateContent;
    }

    /**
     * @return string
     */
    public function getTemplateKeyword() : string {
        return $this->templateKeyword;
    }

    /**
     * @param string $templateKeyword
     */
    public function setTemplateKeyword(string $templateKeyword){
        $this->templateKeyword = $templateKeyword;
    }

    /**
     * @return string
     */
    public function getOpenid() : string {
        return $this->openid;
    }

    /**
     * @param string $openid
     */
    public function setOpenid(string $openid){
        $this->openid = $openid;
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
        $this->redirectUrl = $redirectUrl;
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
        $this->formId = $formId;
    }

    /**
     * @return array
     * @throws \Exception\Wx\WxOpenException
     */
    public function getDetail() : array {
        if(strlen($this->templateId) == 0){
            throw new WxOpenException('模板ID不能为空', ErrorCode::WXOPEN_PARAM_ERROR);
        }
        if(strlen($this->openid) == 0){
            throw new WxOpenException('用户openid不能为空', ErrorCode::WXOPEN_PARAM_ERROR);
        }
        if(strlen($this->formId) == 0){
            throw new WxOpenException('表单ID不能为空', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        return [
            'touser' => $this->openid,
            'template_id' => $this->templateId,
            'page' => $this->redirectUrl,
            'form_id' => $this->formId,
            'data' => $this->templateContent,
            'emphasis_keyword' => $this->templateKeyword,
        ];
    }
}