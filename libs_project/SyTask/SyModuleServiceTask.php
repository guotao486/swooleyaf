<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/10
 * Time: 13:32
 */
namespace SyTask;

use Constant\Project;

class SyModuleServiceTask extends SyModuleTaskBase implements SyModuleTaskInterface {
    public function __construct() {
        parent::__construct();
        $this->moduleTag = Project::MODULE_NAME_SERVICE;
    }

    private function __clone() {
    }

    public function handleTask(array $data) {
        if($data['clear_localuser']){ //清除本地用户信息缓存
            $this->clearLocalUserCache([
                'projects' => $data['projects'],
            ], '');
        }
        if($data['clear_localwxshoptoken']){
            $this->clearLocalWxshopTokenCache([
                'projects' => $data['projects'],
            ], '');
        }
        if($data['clear_localwxopenauthorizertoken']){
            $this->clearLocalWxopenAuthorizerTokenCache([
                'projects' => $data['projects'],
            ], '');
        }
    }
}