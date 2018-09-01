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
use Tool\Tool;

class MiniCodeUpload extends OpenBase {
    public function __construct(){
        parent::__construct();
    }

    private function __clone(){
    }

    /**
     * 代码模板ID
     * @var string
     */
    private $templateId = '';
    /**
     * 自定义代码版本号
     * @var string
     */
    private $userVersion = '';
    /**
     * 自定义代码描述
     * @var string
     */
    private $userDesc = '';

    /**
     * 自定义配置
     * @var array
     */
    private $extData = [];

    /**
     * @return string
     */
    public function getTemplateId() : string {
        return $this->templateId;
    }

    /**
     * @param string $templateId
     */
    public function setTemplateId(string $templateId) {
        $this->templateId = trim($templateId);
    }

    /**
     * @return string
     */
    public function getUserVersion() : string {
        return $this->userVersion;
    }

    /**
     * @param string $userVersion
     */
    public function setUserVersion(string $userVersion){
        $this->userVersion = trim($userVersion);
    }

    /**
     * @return string
     */
    public function getUserDesc() : string {
        return $this->userDesc;
    }

    /**
     * @param string $userDesc
     */
    public function setUserDesc(string $userDesc){
        $this->userDesc = trim($userDesc);
    }

    /**
     * @return array
     */
    public function getExtData() : array {
        return $this->extData;
    }

    /**
     * @param array $extData
     */
    public function setExtData(array $extData){
        $this->extData = $extData;
    }

    /**
     * @return array
     * @throws \Exception\Wx\WxOpenException
     */
    public function getDetail() : array {
        if(strlen($this->templateId) == 0){
            throw new WxOpenException('模板ID不能为空', ErrorCode::WXOPEN_PARAM_ERROR);
        }
        if(empty($this->extData)){
            throw new WxOpenException('自定义配置不能为空', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        return [
            'template_id' => $this->templateId,
            'ext_json' => Tool::jsonEncode($this->extData, JSON_UNESCAPED_UNICODE),
            'user_version' => $this->userVersion,
            'user_desc' => $this->userDesc,
        ];
    }
}