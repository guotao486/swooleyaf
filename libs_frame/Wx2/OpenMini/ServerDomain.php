<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-13
 * Time: 上午12:17
 */
namespace Wx2\OpenMini;

use Constant\ErrorCode;
use Exception\Wx\WxOpenException;
use Tool\Tool;
use Wx2\WxBaseOpenMini;
use Wx2\WxUtilBase;
use Wx2\WxUtilOpenBase;

class ServerDomain extends WxBaseOpenMini {
    /**
     * 应用ID
     * @var string
     */
    private $appId = '';
    /**
     * 修改数据
     * @var array
     */
    private $modifyData = [];

    public function __construct(string $appId){
        parent::__construct();
        $this->serviceUrl = 'https://api.weixin.qq.com/wxa/modify_domain?access_token=';
        $this->appId = $appId;
    }

    public function __clone(){
    }

    /**
     * @param string $action
     * @param array $domains
     * @throws \Exception\Wx\WxOpenException
     */
    public function setModifyData(string $action,array $domains=[]){
        if(!in_array($action, ['add', 'delete', 'set', 'get'])){
            throw new WxOpenException('操作类型不支持', ErrorCode::COMMON_PARAM_ERROR);
        } else if($action != 'get'){
            if(empty($domains)){
                throw new WxOpenException('域名不能为空', ErrorCode::COMMON_PARAM_ERROR);
            }

            $this->modifyData = $domains;
            $this->modifyData['action'] = $action;
        } else {
            $this->modifyData = [
                'action' => $action,
            ];
        }
    }

    public function getDetail() : array {
        if(empty($this->modifyData)){
            throw new WxOpenException('修改数据不能为空', ErrorCode::COMMON_PARAM_ERROR);
        }

        $resArr = [
            'code' => 0,
        ];

        $this->curlConfigs[CURLOPT_URL] = $this->serviceUrl . WxUtilOpenBase::getAuthorizerAccessToken($this->appId);
        $this->curlConfigs[CURLOPT_POSTFIELDS] = Tool::jsonEncode($this->modifyData, JSON_UNESCAPED_UNICODE);
        $this->curlConfigs[CURLOPT_SSL_VERIFYPEER] = false;
        $this->curlConfigs[CURLOPT_SSL_VERIFYHOST] = false;
        $sendRes = WxUtilBase::sendPostReq($this->curlConfigs);
        $sendData = Tool::jsonDecode($sendRes);
        if($sendData['errcode'] == 0){
            $resArr['data'] = $sendData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $sendData['errmsg'];
        }

        return $resArr;
    }
}