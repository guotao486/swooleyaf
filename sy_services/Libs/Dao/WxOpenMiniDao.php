<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/7/17 0017
 * Time: 12:20
 */
namespace Dao;

use Constant\ErrorCode;
use Constant\Project;
use Exception\Common\CheckException;
use Factories\SyBaseMysqlFactory;
use Tool\Tool;
use Traits\SimpleDaoTrait;
use Wx\Open\MiniCodeUpload;
use Wx\WxUtilOpenMini;

class WxOpenMiniDao {
    use SimpleDaoTrait;

    public static function getDraftCodeList(array $data){
        $codeList = WxUtilOpenMini::getDraftCodeList();
        if($codeList['code'] > 0){
            throw new CheckException($codeList['message'], $codeList['code']);
        }

        return $codeList['data']['draft_list'];
    }

    public static function getTemplateCodeList(array $data){
        $codeList = WxUtilOpenMini::getTemplateCodeList();
        if($codeList['code'] > 0){
            throw new CheckException($codeList['message'], $codeList['code']);
        }

        return $codeList['data']['template_list'];
    }

    public static function addTemplateCode(array $data){
        $addRes = WxUtilOpenMini::addTemplateCode($data['draft_id']);
        if($addRes['code'] > 0){
            throw new CheckException($addRes['message'], $addRes['code']);
        }

        return [
            'msg' => '添加成功',
        ];
    }

    public static function delTemplateCode(array $data){
        $delRes = WxUtilOpenMini::deleteTemplateCode($data['template_id']);
        if($delRes['code'] > 0){
            throw new CheckException($delRes['message'], $delRes['code']);
        }

        return [
            'msg' => '删除成功',
        ];
    }

    public static function modifyServerDomain(array $data){
        $modifyRes = WxUtilOpenMini::modifyMiniServerDomain($data['wxmini_appid'], $data['action_type'], $data['domains']);
        if($modifyRes['code'] > 0){
            throw new CheckException($modifyRes['message'], $modifyRes['code']);
        }

        return $modifyRes['data'];
    }

    public static function setWebViewDomain(array $data){
        $setRes = WxUtilOpenMini::setMiniWebViewDomain($data['wxmini_appid'], $data['action_type'], $data['domains']);
        if($setRes['code'] > 0){
            throw new CheckException($setRes['message'], $setRes['code']);
        }

        return $setRes['data'];
    }

    public static function getMiniCategoryList(array $data){
        $getRes = WxUtilOpenMini::getMiniCategory($data['wxmini_appid']);
        if($getRes['code'] > 0){
            throw new CheckException($getRes['message'], $getRes['code']);
        }

        return $getRes['data']['category_list'];
    }

    public static function getMiniPageConfig(array $data){
        $getRes = WxUtilOpenMini::getMiniPageConfig($data['wxmini_appid']);
        if($getRes['code'] > 0){
            throw new CheckException($getRes['message'], $getRes['code']);
        }

        return $getRes['data']['page_list'];
    }

    public static function uploadMiniCode(array $data){
        $codeUpload = new MiniCodeUpload();
        $codeUpload->setTemplateId($data['template_id']);
        $codeUpload->setExtData($data['ext_json']);
        $codeUpload->setUserVersion($data['user_version']);
        $codeUpload->setUserDesc($data['user_desc']);
        $uploadRes = WxUtilOpenMini::uploadMiniCode($data['wxmini_appid'], $codeUpload);
        if($uploadRes['code'] > 0){
            throw new CheckException($uploadRes['message'], $uploadRes['code']);
        }

        $wxMiniConfig = SyBaseMysqlFactory::WxminiConfigEntity();
        $ormResult1 = $wxMiniConfig->getContainer()->getModel()->getOrmDbTable();
        $ormResult1->where('`app_id`=?', [$data['wxmini_appid']]);
        $wxMiniConfig->getContainer()->getModel()->update($ormResult1, [
            'latest_code' => $data['template_id'],
            'audit_id' => '',
            'audit_status' => Project::WXMINI_AUDIT_STATUS_UNDO,
            'audit_desc' => '',
            'option_status' => Project::WXMINI_OPTION_STATUS_UPLOADED,
            'updated' => Tool::getNowTime(),
        ]);
        unset($codeUpload, $ormResult1, $wxMiniConfig);

        return [
            'msg' => '上传小程序代码成功',
        ];
    }

    public static function auditMiniCode(array $data){
        $wxMiniConfig = SyBaseMysqlFactory::WxminiConfigEntity();
        $ormResult1 = $wxMiniConfig->getContainer()->getModel()->getOrmDbTable();
        $ormResult1->where('`app_id`=?', [$data['wxmini_appid']]);
        $wxInfo = $wxMiniConfig->getContainer()->getModel()->findOne($ormResult1);
        if(empty($wxInfo)){
            throw new CheckException('微信信息不存在', ErrorCode::COMMON_PARAM_ERROR);
        } else if($wxInfo['option_status'] != Project::WXMINI_OPTION_STATUS_UPLOADED){
            throw new CheckException('未上传代码', ErrorCode::COMMON_PARAM_ERROR);
        }

        $auditRes = WxUtilOpenMini::auditMiniCode($data['wxmini_appid'], $data['audit_items']);
        if($auditRes['code'] > 0){
            throw new CheckException($auditRes['message'], $auditRes['code']);
        }

        $ormResult2 = $wxMiniConfig->getContainer()->getModel()->getOrmDbTable();
        $ormResult2->where('`app_id`=? AND `option_status`=?', [$data['wxmini_appid'], Project::WXMINI_OPTION_STATUS_UPLOADED,]);
        $wxMiniConfig->getContainer()->getModel()->update($ormResult2, [
            'audit_id' => $auditRes['data']['auditid'],
            'audit_status' => Project::WXMINI_AUDIT_STATUS_HANDING,
            'audit_desc' => '',
            'option_status' => Project::WXMINI_OPTION_STATUS_APPLY_AUDIT,
            'updated' => Tool::getNowTime(),
        ]);
        unset($ormResult2, $ormResult1, $wxMiniConfig);

        return [
            'audit_id' => $auditRes['data']['auditid'],
        ];
    }

    public static function refreshMiniCodeAuditResult(array $data){
        $wxMiniConfig = SyBaseMysqlFactory::WxminiConfigEntity();
        $ormResult1 = $wxMiniConfig->getContainer()->getModel()->getOrmDbTable();
        $ormResult1->where('`app_id`=?', [$data['wxmini_appid']]);
        $wxInfo = $wxMiniConfig->getContainer()->getModel()->findOne($ormResult1);
        if(empty($wxInfo)){
            throw new CheckException('微信信息不存在', ErrorCode::COMMON_PARAM_ERROR);
        } else if($wxInfo['audit_id'] != $data['audit_id']){
            throw new CheckException('微信appid和审核ID不匹配', ErrorCode::COMMON_PARAM_ERROR);
        } else if($wxInfo['audit_status'] == Project::WXMINI_AUDIT_STATUS_UNDO){
            throw new CheckException('审核状态不支持', ErrorCode::COMMON_PARAM_ERROR);
        } else if(in_array($wxInfo['audit_status'], [Project::WXMINI_AUDIT_STATUS_SUCCESS, Project::WXMINI_AUDIT_STATUS_FAIL,])){
            return [
                'audit_status' => $wxInfo['audit_status'],
                'audit_desc' => $wxInfo['audit_desc'],
                'msg' => '更新审核结果成功',
            ];
        }

        $getRes = WxUtilOpenMini::getMiniAuditStatus($data['wxmini_appid'], $data['audit_id']);
        if($getRes['code'] > 0){
            throw new CheckException($getRes['message'], $getRes['code']);
        }

        $ormResult2 = $wxMiniConfig->getContainer()->getModel()->getOrmDbTable();
        $ormResult2->where('`app_id`=? AND `audit_status`=?', [$data['wxmini_appid'], Project::WXMINI_AUDIT_STATUS_HANDING]);
        if($getRes['data']['status'] == Project::WXMINI_AUDIT_STATUS_FAIL){
            $wxMiniConfig->getContainer()->getModel()->update($ormResult2, [
                'audit_status' => Project::WXMINI_AUDIT_STATUS_FAIL,
                'audit_desc' => $getRes['data']['reason'],
                'option_status' => Project::WXMINI_OPTION_STATUS_AUDIT_FAIL,
                'updated' => Tool::getNowTime(),
            ]);
        } else if($getRes['data']['status'] == Project::WXMINI_AUDIT_STATUS_SUCCESS){
            $wxMiniConfig->getContainer()->getModel()->update($ormResult2, [
                'audit_status' => Project::WXMINI_AUDIT_STATUS_SUCCESS,
                'audit_desc' => '',
                'option_status' => Project::WXMINI_OPTION_STATUS_AUDIT_SUCCESS,
                'updated' => Tool::getNowTime(),
            ]);
        }
        unset($ormResult2, $ormResult1, $wxMiniConfig);

        return [
            'msg' => '更新审核结果成功',
            'audit_status' => $getRes['data']['status'],
            'audit_desc' => isset($getRes['data']['reason']) ? $getRes['data']['reason'] : '',
        ];
    }

    public static function releaseMiniCode(array $data){
        $wxMiniConfig = SyBaseMysqlFactory::WxminiConfigEntity();
        $ormResult1 = $wxMiniConfig->getContainer()->getModel()->getOrmDbTable();
        $ormResult1->where('`app_id`=?', [$data['wxmini_appid']]);
        $wxInfo = $wxMiniConfig->getContainer()->getModel()->findOne($ormResult1);
        if(empty($wxInfo)){
            throw new CheckException('微信信息不存在', ErrorCode::COMMON_PARAM_ERROR);
        } else if($wxInfo['option_status'] != Project::WXMINI_OPTION_STATUS_AUDIT_SUCCESS){
            throw new CheckException('只有审核成功才允许发布', ErrorCode::COMMON_PARAM_ERROR);
        }

        $releaseRes = WxUtilOpenMini::releaseMiniCode($data['wxmini_appid']);
        if($releaseRes['code'] > 0){
            throw new CheckException($releaseRes['message'], $releaseRes['code']);
        }


        $ormResult2 = $wxMiniConfig->getContainer()->getModel()->getOrmDbTable();
        $ormResult2->where('`app_id`=? AND `option_status`=?', [$data['wxmini_appid'], Project::WXMINI_OPTION_STATUS_AUDIT_SUCCESS,]);
        $wxMiniConfig->getContainer()->getModel()->update($ormResult2, [
            'option_status' => Project::WXMINI_OPTION_STATUS_RELEASED,
            'updated' => Tool::getNowTime(),
        ]);
        unset($ormResult2, $ormResult1, $wxMiniConfig);

        return $releaseRes['data'];
    }

    public static function preUploadMiniCode(array $data){
        $resArr = [
            'app_id' => '',
        ];

        $wxMiniConfig = SyBaseMysqlFactory::WxminiConfigEntity();
        $ormResult1 = $wxMiniConfig->getContainer()->getModel()->getOrmDbTable();
        $ormResult1->where('`status`=? AND `wtype`=? AND `latest_code`<>?', [Project::WXMINI_STATUS_ENABLE, Project::WXMINI_TYPE_SHOP_MINI, $data['template_id'],])
                   ->order('`id` ASC');
        $wxInfo = $wxMiniConfig->getContainer()->getModel()->findOne($ormResult1);
        if(!empty($wxInfo)){
            $resArr['app_id'] = $wxInfo['app_id'];
        }
        unset($ormResult1, $wxMiniConfig);

        return $resArr;
    }

    public static function preAuditMiniCode(array $data){
        $resArr = [
            'app_id' => '',
        ];

        $wxMiniConfig = SyBaseMysqlFactory::WxminiConfigEntity();;
        $ormResult1 = $wxMiniConfig->getContainer()->getModel()->getOrmDbTable();
        $ormResult1->where('`wtype`=? AND `status`=? AND `option_status`=?', [Project::WXMINI_TYPE_SHOP_MINI, Project::WXMINI_STATUS_ENABLE, Project::WXMINI_OPTION_STATUS_UPLOADED,])
                   ->order('`id` ASC');
        $wxInfo = $wxMiniConfig->getContainer()->getModel()->findOne($ormResult1);
        if(empty($wxInfo)){
            unset($ormResult1, $wxMiniConfig);

            return $resArr;
        }

        $resArr['app_id'] = $wxInfo['app_id'];

        $getRes = WxUtilOpenMini::getMiniCategory($wxInfo['app_id']);
        if($getRes['code'] > 0){
            throw new CheckException($getRes['message'], $getRes['code']);
        } else if(empty($getRes['data']['category_list'])){
            throw new CheckException('可选类目为空', ErrorCode::COMMON_PARAM_ERROR);
        }
        unset($ormResult1, $bindWx);

        $resArr['items'] = [
            0 => $getRes['data']['category_list'][0],
        ];
        $resArr['items'][0]['address'] = 'pages/index/index';
        $resArr['items'][0]['tag'] = '小名片';
        $resArr['items'][0]['title'] = '小名片商城';

        return $resArr;
    }

    public static function preRefreshMiniCodeAuditResult(array $data){
        $resArr = [
            'app_id' => '',
        ];

        $wxMiniConfig = SyBaseMysqlFactory::WxminiConfigEntity();
        $ormResult1 = $wxMiniConfig->getContainer()->getModel()->getOrmDbTable();
        $ormResult1->where('`wtype`=? AND `status`=? AND `audit_status`=?', [Project::WXMINI_TYPE_SHOP_MINI, Project::WXMINI_STATUS_ENABLE, Project::WXMINI_AUDIT_STATUS_HANDING,])
                   ->order('`id` ASC');
        $wxInfo = $wxMiniConfig->getContainer()->getModel()->findOne($ormResult1);
        unset($ormResult1, $wxMiniConfig);
        if(!empty($wxInfo)){
            $resArr['app_id'] = $wxInfo['app_id'];
            $resArr['audit_id'] = $wxInfo['audit_id'];
        }

        return $resArr;
    }

    public static function preReleaseMiniCode(array $data){
        $resArr = [
            'app_id' => '',
        ];

        $wxMiniConfig = SyBaseMysqlFactory::WxminiConfigEntity();
        $ormResult1 = $wxMiniConfig->getContainer()->getModel()->getOrmDbTable();
        $ormResult1->where('`wtype`=? AND `status`=? AND `option_status`=?', [Project::WXMINI_TYPE_SHOP_MINI, Project::WXMINI_STATUS_ENABLE, Project::WXMINI_OPTION_STATUS_AUDIT_SUCCESS,])
                   ->order('`id` ASC');
        $wxInfo = $wxMiniConfig->getContainer()->getModel()->findOne($ormResult1);
        unset($ormResult1, $wxMiniConfig);
        if(!empty($wxInfo)){
            $resArr['app_id'] = $wxInfo['app_id'];
        }

        return $resArr;
    }
}