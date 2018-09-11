<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-12
 * Time: 上午12:07
 */
namespace Wx2\Shop;

use Constant\ErrorCode;
use Exception\Wx\WxException;
use Tool\Tool;
use Wx2\WxBaseShop;
use Wx2\WxUtilBase;
use Wx2\WxUtilBaseAlone;

class TemplateMsg extends WxBaseShop {
    /**
     * 用户openid
     * @var string
     */
    private $openid = '';
    /**
     * 模版ID
     * @var string
     */
    private $template_id = '';
    /**
     * 重定向链接地址
     * @var string
     */
    private $redirect_url = '';
    /**
     * 模版数据
     * @var array
     */
    private $template_data = [];

    public function __construct(){
        parent::__construct();
        $this->serviceUrl = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=';
        $this->reqData['url'] = '';
        $this->reqData['data'] = [];
    }

    public function __clone(){
    }

    /**
     * @param string $openid
     * @throws \Exception\Wx\WxException
     */
    public function setOpenid(string $openid) {
        if (preg_match('/^[0-9a-zA-Z\-\_]{28}$/', $openid) > 0) {
            $this->reqData['touser'] = $openid;
        } else {
            throw new WxException('用户openid不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $templateId
     * @throws \Exception\Wx\WxException
     */
    public function setTemplateId(string $templateId) {
        if (strlen($templateId) > 0) {
            $this->reqData['template_id'] = $templateId;
        } else {
            throw new WxException('模版ID不能为空', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $redirectUrl
     * @throws \Exception\Wx\WxException
     */
    public function setRedirectUrl(string $redirectUrl) {
        if (preg_match('/^(http|https|ftp)\:\/\/\S+$/', $redirectUrl) > 0) {
            $this->reqData['url'] = $redirectUrl;
        } else {
            throw new WxException('重定向链接不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * 模板参数内容
     * 数据格式如下:
     * [
     *     'first' => [
     *         'value' => '1234',
     *         'color' => '#743A3A',
     *     ],
     *     'remark' => [
     *         'value' => '1234',
     *         'color' => '#743A3A',
     *     ],
     * ]
     *
     *@param array $templateData
     */
    public function setTemplateData(array $templateData) {
        $this->reqData['data'] = $templateData;
    }

    public function getDetail(string $appId='') : array {
        if(strlen($appId) == 0){
            throw new WxException('应用ID不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if (!isset($this->reqData['touser'])) {
            throw new WxException('用户openid不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if (!isset($this->reqData['template_id'])) {
            throw new WxException('模版ID不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr = [
            'code' => 0
        ];

        $this->curlConfigs[CURLOPT_URL] = $this->serviceUrl . WxUtilBaseAlone::getAccessToken($appId);
        $this->curlConfigs[CURLOPT_POSTFIELDS] = Tool::jsonEncode($this->reqData, JSON_UNESCAPED_UNICODE);
        $sendRes = WxUtilBase::sendPostReq($this->curlConfigs);
        $sendData = Tool::jsonDecode($sendRes);
        if ($sendData['errcode'] == 0) {
            $resData['data'] = $sendData;
        } else {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $sendData['errmsg'];
        }

        return $resArr;
    }
}