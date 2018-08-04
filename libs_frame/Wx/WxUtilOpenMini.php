<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-7-15
 * Time: 上午9:38
 */
namespace Wx;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxOpenException;
use Tool\Tool;
use Traits\SimpleTrait;
use Wx\Open\MiniCodeUpload;
use Wx\Open\MiniTemplateMsg;

final class WxUtilOpenMini extends WxUtilOpenBase {
    use SimpleTrait;

    private static $urlGetMiniDraftCodeList = 'https://api.weixin.qq.com/wxa/gettemplatedraftlist?access_token=';
    private static $urlGetMiniTemplateCodeList = 'https://api.weixin.qq.com/wxa/gettemplatelist?access_token=';
    private static $urlAddMiniTemplateCode = 'https://api.weixin.qq.com/wxa/addtotemplate?access_token=';
    private static $urlDelMiniTemplateCode = 'https://api.weixin.qq.com/wxa/deletetemplate?access_token=';
    private static $urlModifyMiniServerDomain = 'https://api.weixin.qq.com/wxa/modify_domain?access_token=';
    private static $urlSetMiniWebViewDomain = 'https://api.weixin.qq.com/wxa/setwebviewdomain?access_token=';
    private static $urlRebindMiniAdmin = 'https://api.weixin.qq.com/cgi-bin/account/componentrebindadmin?access_token=';
    private static $urlUploadMiniCode = 'https://api.weixin.qq.com/wxa/commit?access_token=';
    private static $urlGetMiniCategory = 'https://api.weixin.qq.com/wxa/get_category?access_token=';
    private static $urlGetMiniPageConfig = 'https://api.weixin.qq.com/wxa/get_page?access_token=';
    private static $urlAuditMiniCode = 'https://api.weixin.qq.com/wxa/submit_audit?access_token=';
    private static $urlGetMiniAuditStatus = 'https://api.weixin.qq.com/wxa/get_auditstatus?access_token=';
    private static $urlReleaseMiniCode = 'https://api.weixin.qq.com/wxa/release?access_token=';
    private static $urlChangeMiniVisitStatus = 'https://api.weixin.qq.com/wxa/change_visitstatus?access_token=';
    private static $urlRollbackMiniCode = 'https://api.weixin.qq.com/wxa/revertcoderelease?access_token=';
    private static $urlSetMiniSupportVersion = 'https://api.weixin.qq.com/cgi-bin/wxopen/setweappsupportversion?access_token=';
    private static $urlUndoAuditMiniCode = 'https://api.weixin.qq.com/wxa/undocodeaudit?access_token=';
    private static $urlGrayReleaseMiniCode = 'https://api.weixin.qq.com/wxa/grayrelease?access_token=';
    private static $urlRevertGrayReleaseMiniCode = 'https://api.weixin.qq.com/wxa/revertgrayrelease?access_token=';
    private static $urlGetMiniCodeReleasePlan = 'https://api.weixin.qq.com/wxa/getgrayreleaseplan?access_token=';
    private static $urlChangeMiniSearchStatus = 'https://api.weixin.qq.com/wxa/changewxasearchstatus?access_token=';
    private static $urlGetMiniSearchStatus = 'https://api.weixin.qq.com/wxa/getwxasearchstatus?access_token=';
    private static $urlMiniPlugin = 'https://api.weixin.qq.com/wxa/plugin?access_token=';
    private static $urlGetMiniTemplateMsgList = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token=';
    private static $urlDelMiniTemplateMsg = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/del?access_token=';
    private static $urlSendMiniTemplateMsg = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=';

    /**
     * 获取草稿代码列表
     * @return array
     */
    public static function getDraftCodeList() : array {
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlGetMiniDraftCodeList . self::getComponentAccessToken(WxConfigSingleton::getInstance()->getOpenCommonConfig()->getAppId());
        $getRes = self::sendGetReq($url);
        $getData = Tool::jsonDecode($getRes);
        if($getData['errcode'] == 0){
            $resArr['data'] = $getData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_GET_ERROR;
            $resArr['message'] = $getData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 获取模板代码列表
     * @return array
     */
    public static function getTemplateCodeList() : array {
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlGetMiniTemplateCodeList . self::getComponentAccessToken(WxConfigSingleton::getInstance()->getOpenCommonConfig()->getAppId());
        $getRes = self::sendGetReq($url);
        $getData = Tool::jsonDecode($getRes);
        if($getData['errcode'] == 0){
            $resArr['data'] = $getData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_GET_ERROR;
            $resArr['message'] = $getData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 添加模板代码
     * @param string $draftId 草稿ID
     * @return array
     */
    public static function addTemplateCode(string $draftId) : array {
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlAddMiniTemplateCode . self::getComponentAccessToken(WxConfigSingleton::getInstance()->getOpenCommonConfig()->getAppId());
        $addRes = self::sendPostReq($url, 'json', [
            'draft_id' => $draftId,
        ], [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $addData = Tool::jsonDecode($addRes);
        if($addData['errcode'] == 0){
            $resArr['data'] = $addData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $addData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 删除模板代码
     * @param string $templateId 模板ID
     * @return array
     */
    public static function deleteTemplateCode(string $templateId) : array {
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlDelMiniTemplateCode . self::getComponentAccessToken(WxConfigSingleton::getInstance()->getOpenCommonConfig()->getAppId());
        $delRes = self::sendPostReq($url, 'json', [
            'template_id' => $templateId,
        ], [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $delData = Tool::jsonDecode($delRes);
        if($delData['errcode'] == 0){
            $resArr['data'] = $delData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $delData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 设置小程序服务器域名
     * @param string $appId 小程序app id
     * @param string $action 操作类型 add:添加 delete:删除 set:覆盖 get:获取
     * @param array $domains 域名列表
     * @return array
     * @throws \Exception\Wx\WxOpenException
     */
    public static function modifyMiniServerDomain(string $appId,string $action,array $domains=[]){
        if(!in_array($action, ['add', 'delete', 'set', 'get'])){
            throw new WxOpenException('操作类型不支持', ErrorCode::COMMON_PARAM_ERROR);
        } else if($action != 'get'){
            if(empty($domains)){
                throw new WxOpenException('域名不能为空', ErrorCode::COMMON_PARAM_ERROR);
            }

            $modifyData = $domains;
            $modifyData['action'] = $action;
        } else {
            $modifyData = [
                'action' => $action,
            ];
        }

        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlModifyMiniServerDomain . self::getAuthorizerAccessToken($appId);
        $sendRes = self::sendPostReq($url, 'json', $modifyData, [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $sendData = Tool::jsonDecode($sendRes);
        if($sendData['errcode'] == 0){
            $resArr['data'] = $sendData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $sendData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 设置小程序业务域名
     * @param string $appId 小程序app id
     * @param string $action 操作类型 add:添加 delete:删除 set:覆盖 get:获取
     * @param array $domains 域名列表
     * @return array
     * @throws \Exception\Wx\WxOpenException
     */
    public static function setMiniWebViewDomain(string $appId,string $action,array $domains=[]){
        if(!in_array($action, ['add', 'delete', 'set', 'get'])){
            throw new WxOpenException('操作类型不支持', ErrorCode::COMMON_PARAM_ERROR);
        } else if($action != 'get'){
            if(empty($domains)){
                throw new WxOpenException('域名不能为空', ErrorCode::COMMON_PARAM_ERROR);
            }

            $data = $domains;
            $data['action'] = $action;
        } else {
            $data = [
                'action' => $action,
            ];
        }

        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlSetMiniWebViewDomain . self::getAuthorizerAccessToken($appId);
        $setRes = self::sendPostReq($url, 'json', $data, [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $setData = Tool::jsonDecode($setRes);
        if($setData['errcode'] == 0){
            $resArr['data'] = $setData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $setData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 获取小程序换绑管理员地址
     * @param string $appId 小程序app id
     * @return string
     */
    public static function getMiniRebindAdminUrl(string $appId){
        return 'https://mp.weixin.qq.com/wxopen/componentrebindadmin?appid=' . $appId . '&component_appid='
               . WxConfigSingleton::getInstance()->getOpenCommonConfig()->getAppId() . '&redirect_uri='
               . urlencode(WxConfigSingleton::getInstance()->getOpenCommonConfig()->getUrlMiniRebindAdmin());
    }

    /**
     * 换绑小程序管理员
     * @param string $taskId
     * @return array
     */
    public static function rebindMiniAdmin(string $taskId){
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlRebindMiniAdmin . self::getComponentAccessToken(WxConfigSingleton::getInstance()->getOpenCommonConfig()->getAppId());
        $rebindRes = self::sendPostReq($url, 'json', [
            'taskid' => $taskId,
        ], [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $rebindData = Tool::jsonDecode($rebindRes);
        if($rebindData['errcode'] == 0){
            $resArr['data'] = $rebindData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $rebindData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 上传小程序代码
     * @param string $appId 小程序app id
     * @param \Wx\Open\MiniCodeUpload $codeUpload
     * @return array
     */
    public static function uploadMiniCode(string $appId,MiniCodeUpload $codeUpload){
        $resArr = [
            'code' => 0,
        ];

        $uploadData = $codeUpload->getDetail();
        $url = self::$urlUploadMiniCode . self::getAuthorizerAccessToken($appId);
        $uploadRes = self::sendPostReq($url, 'json', $uploadData, [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $uploadData = Tool::jsonDecode($uploadRes);
        if($uploadData['errcode'] == 0){
            $resArr['data'] = $uploadData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $uploadData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 获取小程序的可选类目
     * @param string $appId 小程序app id
     * @return array
     */
    public static function getMiniCategory(string $appId){
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlGetMiniCategory . self::getAuthorizerAccessToken($appId);
        $getRes = self::sendGetReq($url);
        $getData = Tool::jsonDecode($getRes);
        if($getData['errcode'] == 0){
            $resArr['data'] = $getData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_GET_ERROR;
            $resArr['message'] = $getData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 获取第三方提交小程序代码的页面配置
     * @param string $appId 小程序app id
     * @return array
     */
    public static function getMiniPageConfig(string $appId) : array {
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlGetMiniPageConfig . self::getAuthorizerAccessToken($appId);
        $getRes = self::sendGetReq($url);
        $getData = Tool::jsonDecode($getRes);
        if($getData['errcode'] == 0){
            $resArr['data'] = $getData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_GET_ERROR;
            $resArr['message'] = $getData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 审核小程序代码
     * @param string $appId 小程序app id
     * @param array $auditList 审核列表
     * @return array
     * @throws \Exception\Wx\WxOpenException
     */
    public static function auditMiniCode(string $appId,array $auditList){
        $auditNum = count($auditList);
        if($auditNum == 0){
            throw new WxOpenException('审核列表不能为空', ErrorCode::WXOPEN_PARAM_ERROR);
        } else if($auditNum > 5){
            throw new WxOpenException('审核列表数量不能超过5个', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlAuditMiniCode . self::getAuthorizerAccessToken($appId);
        $auditRes = self::sendPostReq($url, 'json', [
            'item_list' => $auditList,
        ], [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $auditData = Tool::jsonDecode($auditRes);
        if($auditData['errcode'] == 0){
            $resArr['data'] = $auditData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $auditData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 查询小程序的审核状态
     * @param string $appId 小程序app id
     * @param string $auditId 审核id
     * @return array
     */
    public static function getMiniAuditStatus(string $appId,string $auditId){
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlGetMiniAuditStatus . self::getAuthorizerAccessToken($appId);
        $getRes = self::sendPostReq($url, 'json', [
            'auditid' => $auditId,
        ], [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $getData = Tool::jsonDecode($getRes);
        if($getData['errcode'] == 0){
            $resArr['data'] = $getData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $getData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 发布小程序代码
     * @param string $appId 小程序app id
     * @return array
     */
    public static function releaseMiniCode(string $appId){
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlReleaseMiniCode . self::getAuthorizerAccessToken($appId);
        $releaseRes = self::sendPostReq($url, 'json', [], [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $releaseData = Tool::jsonDecode($releaseRes);
        if($releaseData['errcode'] == 0){
            $resArr['data'] = $releaseData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $releaseData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 修改小程序代码的可见状态
     * @param string $appId 小程序app id
     * @param string $visitStatus 访问状态,close:不可见 open:可见
     * @return array
     * @throws \Exception\Wx\WxOpenException
     */
    public static function changeMiniVisitStatus(string $appId,string $visitStatus){
        if(!in_array($visitStatus, ['close', 'open'])){
            throw new WxOpenException('访问状态不支持', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlChangeMiniVisitStatus . self::getAuthorizerAccessToken($appId);
        $changeRes = self::sendPostReq($url, 'json', [
            'action' => $visitStatus,
        ], [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $changeData = Tool::jsonDecode($changeRes);
        if($changeData['errcode'] == 0){
            $resArr['data'] = $changeData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $changeData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 回退小程序代码
     * @param string $appId 小程序app id
     * @return array
     */
    public static function rollbackMiniCode(string $appId){
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlRollbackMiniCode . self::getAuthorizerAccessToken($appId);
        $rollbackRes = self::sendGetReq($url);
        $rollbackData = Tool::jsonDecode($rollbackRes);
        if($rollbackData['errcode'] == 0){
            $resArr['data'] = $rollbackData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_GET_ERROR;
            $resArr['message'] = $rollbackData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 设置小程序最低基础库版本
     * @param string $appId 小程序app id
     * @param string $version 最低版本号
     * @return array
     */
    public static function setMiniSupportVersion(string $appId,string $version){
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlSetMiniSupportVersion . self::getAuthorizerAccessToken($appId);
        $setRes = self::sendPostReq($url, 'json', [
            'version' => $version,
        ], [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $setData = Tool::jsonDecode($setRes);
        if($setData['errcode'] == 0){
            $resArr['data'] = $setData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $setData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 撤回小程序代码审核
     * @param string $appId 小程序app id
     * @return array
     */
    public static function undoAuditMiniCode(string $appId){
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlUndoAuditMiniCode . self::getAuthorizerAccessToken($appId);
        $undoRes = self::sendGetReq($url);
        $undoData = Tool::jsonDecode($undoRes);
        if($undoData['errcode'] == 0){
            $resArr['data'] = $undoData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_GET_ERROR;
            $resArr['message'] = $undoData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 灰度发布小程序代码
     * @param string $appId 小程序app id
     * @param int $percentage 灰度百分比
     * @return array
     * @throws \Exception\Wx\WxOpenException
     */
    public static function grayReleaseMiniCode(string $appId,int $percentage){
        if (($percentage < 1) ||($percentage > 100)) {
            throw new WxOpenException('灰度百分比不合法', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlGrayReleaseMiniCode . self::getAuthorizerAccessToken($appId);
        $releaseRes = self::sendPostReq($url, 'json', [
            'gray_percentage' => $percentage,
        ], [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $releaseData = Tool::jsonDecode($releaseRes);
        if($releaseData['errcode'] == 0){
            $resArr['data'] = $releaseData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $releaseData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 取消灰度发布小程序代码
     * @param string $appId 小程序app id
     * @return array
     */
    public static function revertGrayReleaseMiniCode(string $appId){
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlRevertGrayReleaseMiniCode . self::getAuthorizerAccessToken($appId);
        $revertRes = self::sendGetReq($url);
        $revertData = Tool::jsonDecode($revertRes);
        if($revertData['errcode'] == 0){
            $resArr['data'] = $revertData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_GET_ERROR;
            $resArr['message'] = $revertData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 查询小程序代码灰度发布详情
     * @param string $appId 小程序app id
     * @return array
     */
    public static function getMiniCodeReleasePlan(string $appId){
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlGetMiniCodeReleasePlan . self::getAuthorizerAccessToken($appId);
        $getRes = self::sendGetReq($url);
        $getData = Tool::jsonDecode($getRes);
        if($getData['errcode'] == 0){
            $resArr['data'] = $getData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_GET_ERROR;
            $resArr['message'] = $getData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 设置小程序搜索状态
     * @param string $appId 小程序app id
     * @param int $searchStatus 搜索状态,0:可搜索 1:不可搜索，
     * @return array
     * @throws \Exception\Wx\WxOpenException
     */
    public static function changeMiniSearchStatus(string $appId,int $searchStatus){
        if(!in_array($searchStatus, [0, 1])){
            throw new WxOpenException('搜索状态不合法', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlChangeMiniSearchStatus . self::getAuthorizerAccessToken($appId);
        $changeRes = self::sendPostReq($url, 'json', [
            'status' => $searchStatus,
        ], [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $changeData = Tool::jsonDecode($changeRes);
        if($changeData['errcode'] == 0){
            $resArr['data'] = $changeData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $changeData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 获取小程序搜索状态
     * @param string $appId 小程序app id
     * @return array
     */
    public static function getMiniSearchStatus(string $appId){
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlGetMiniSearchStatus . self::getAuthorizerAccessToken($appId);
        $getRes = self::sendGetReq($url);
        $getData = Tool::jsonDecode($getRes);
        if($getData['errcode'] == 0){
            $resArr['data'] = $getData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_GET_ERROR;
            $resArr['message'] = $getData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 添加小程序插件
     * @param string $appId 小程序app id
     * @param string $pluginAppId 插件appid
     * @return array
     */
    public static function addMiniPlugin(string $appId,string $pluginAppId){
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlMiniPlugin . self::getAuthorizerAccessToken($appId);
        $addRes = self::sendPostReq($url, 'json', [
            'action' => 'apply',
            'plugin_appid' => $pluginAppId,
        ], [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $addData = Tool::jsonDecode($addRes);
        if($addData['errcode'] == 0){
            $resArr['data'] = $addData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $addData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 查询小程序的插件列表
     * @param string $appId 小程序app id
     * @return array
     */
    public static function getMiniPluginList(string $appId){
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlMiniPlugin . self::getAuthorizerAccessToken($appId);
        $getRes = self::sendPostReq($url, 'json', [
            'action' => 'list',
        ], [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $getData = Tool::jsonDecode($getRes);
        if($getData['errcode'] == 0){
            $resArr['data'] = $getData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $getData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 删除小程序的插件
     * @param string $appId 小程序app id
     * @param string $pluginAppId 插件appid
     * @return array
     */
    public static function delMiniPlugin(string $appId,string $pluginAppId){
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlMiniPlugin . self::getAuthorizerAccessToken($appId);
        $delRes = self::sendPostReq($url, 'json', [
            'action' => 'unbind',
            'plugin_appid' => $pluginAppId,
        ], [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $delData = Tool::jsonDecode($delRes);
        if($delData['errcode'] == 0){
            $resArr['data'] = $delData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $delData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 获取小程序的模板消息列表
     * @param string $appId 小程序app id
     * @param int $page 页数
     * @param int $limit 分页限制
     * @return array
     */
    public static function getMiniTemplateMsgList(string $appId,int $page,int $limit){
        $resArr = [
            'code' => 0,
        ];

        $offset = ($page - 1) * $limit;
        $url = self::$urlGetMiniTemplateMsgList . self::getAuthorizerAccessToken($appId);
        $getRes = self::sendPostReq($url, 'json', [
            'offset' => $offset,
            'count' => $limit,
        ], [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $getData = Tool::jsonDecode($getRes);
        if($getData['errcode'] == 0){
            $resArr['data'] = $getData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $getData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 删除小程序的模板消息
     * @param string $appId 小程序app id
     * @param string $templateId 模板ID
     * @return array
     */
    public static function delMiniTemplateMsg(string $appId,string $templateId){
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlDelMiniTemplateMsg . self::getAuthorizerAccessToken($appId);
        $delRes = self::sendPostReq($url, 'json', [
            'template_id' => $templateId,
        ], [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $delData = Tool::jsonDecode($delRes);
        if($delData['errcode'] == 0){
            $resArr['data'] = $delData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $delData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 发送小程序模板消息
     * @param string $appId 小程序app id
     * @param \Wx\Open\MiniTemplateMsg $templateMsg
     * @return array
     */
    public static function sendMiniTemplateMsg(string $appId,MiniTemplateMsg $templateMsg){
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlSendMiniTemplateMsg . self::getAuthorizerAccessToken($appId);
        $sendRes = self::sendPostReq($url, 'json', $templateMsg->getDetail(), [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
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