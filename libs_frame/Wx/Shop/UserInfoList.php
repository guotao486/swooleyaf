<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-12
 * Time: 上午12:36
 */
namespace Wx\Shop;

use Constant\ErrorCode;
use Exception\Wx\WxException;
use Tool\Tool;
use Wx\WxBaseShop;
use Wx\WxUtilBase;
use Wx\WxUtilBaseAlone;

class UserInfoList extends WxBaseShop {
    /**
     * 用户openid列表
     * @var array
     */
    private $openidList = [];

    public function __construct(){
        parent::__construct();
        $this->serviceUrl = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=';
    }

    public function __clone(){
    }

    /**
     * @param array $openidList
     */
    public function setOpenidList(array $openidList) {
        foreach ($openidList as $eOpenid) {
            if (preg_match('/^[0-9a-zA-Z\-\_]{28}$/', $eOpenid) > 0) {
                $this->openidList[$eOpenid] = 1;
            }
        }
    }

    public function getDetail(string $appId='') : array {
        if(strlen($appId) == 0){
            throw new WxException('应用ID不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if(empty($this->openidList)){
            throw new WxException('用户openid列表不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $this->reqData['user_list'] = [];
        foreach ($this->openidList as $eOpenid => $val) {
            $this->reqData['user_list'][] = [
                'openid' => $eOpenid,
                'lang' => 'zh-CN',
            ];
        }

        $resArr = [
            'code' => 0
        ];

        $this->curlConfigs[CURLOPT_URL] = $this->serviceUrl . WxUtilBaseAlone::getAccessToken($appId);
        $this->curlConfigs[CURLOPT_POSTFIELDS] = Tool::jsonEncode($this->reqData, JSON_UNESCAPED_UNICODE);
        $sendRes = WxUtilBase::sendPostReq($this->curlConfigs);
        $sendData = Tool::jsonDecode($sendRes);
        if (isset($sendData['user_info_list'])) {
            $resArr['data'] = $sendData['user_info_list'];
        } else {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $sendData['errmsg'];
        }

        return $resArr;
    }
}