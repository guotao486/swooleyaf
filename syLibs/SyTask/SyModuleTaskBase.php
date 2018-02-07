<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-11-14
 * Time: 下午7:06
 */
namespace SyTask;

use Constant\Server;
use Tool\SyPack;
use Tool\Tool;

abstract class SyModuleTaskBase {
    /**
     * @var \Tool\SyPack
     */
    protected $syPack = null;
    /**
     * @var string
     */
    protected $moduleTag = '';

    public function __construct() {
        $this->syPack = new SyPack();
    }

    private function __clone() {
    }

    public function sendSyHttpReq(string $url,array $params,$method='GET') {
        $ch = curl_init();
        $sendUrl = $url;
        if(($method == 'GET') && !empty($params)){
            $sendUrl .= '?' . http_build_query($params);
        }

        curl_setopt($ch, CURLOPT_URL, $sendUrl);
        if($method == 'POST'){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 2000);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        $errNo = curl_errno($ch);
        curl_close($ch);

        return $errNo == 0 ? $res : false;
    }

    public function sendSyTaskReq(string $host,int $port,string $taskStr,string $protocol) {
        if ($protocol == 'http') {
            $url = 'http://' . $host . ':' . $port;
            Tool::sendSyHttpTaskReq($url, $taskStr);
        } else {
            Tool::sendSyRpcReq($host, $port, $taskStr);
        }
    }

    protected function handleRefreshWxCache(array $data,string $moduleTag) {
        if(strlen($moduleTag) == 0){
            //刷新微信access token缓存
            $this->syPack->setCommandAndData(SyPack::COMMAND_TYPE_RPC_CLIENT_SEND_API_REQ, [
                'api_module' => Server::MODULE_NAME_API,
                'api_uri' => '/refreshcache',
                'api_method' => 'POST',
                'api_params' => [
                    'key' => 'wx01_' . $data['app_id'],
                    'value' => $data['access_token'],
                ],
            ]);
            $accessTokenStr = $this->syPack->packData();
            $this->syPack->init();

            //刷新微信js ticket缓存
            $this->syPack->setCommandAndData(SyPack::COMMAND_TYPE_RPC_CLIENT_SEND_API_REQ, [
                'api_module' => Server::MODULE_NAME_API,
                'api_uri' => '/refreshcache',
                'api_method' => 'POST',
                'api_params' => [
                    'key' => 'wx02_' . $data['app_id'],
                    'value' => $data['js_ticket'],
                ],
            ]);
            $jsTicketStr = $this->syPack->packData();
            $this->syPack->init();

            foreach ($data['projects'] as $eProject) {
                Tool::sendSyRpcReq($eProject['host'], $eProject['port'], $accessTokenStr);
                Tool::sendSyRpcReq($eProject['host'], $eProject['port'], $jsTicketStr);
            }
        } else {
            foreach ($data['projects'] as $eProject) {
                $url = 'http://' . $eProject['host'] . ':' . $eProject['port'] . '/refreshcache';
                $this->sendSyHttpReq($url, [
                    'key' => 'wx01_' . $data['app_id'],
                    'value' => $data['access_token'],
                ], 'POST');
                $this->sendSyHttpReq($url, [
                    'key' => 'wx02_' . $data['app_id'],
                    'value' => $data['js_ticket'],
                ], 'POST');
            }
        }
    }

    protected function clearLocalUserCache(array $data,string $moduleTag) {
        if(strlen($moduleTag) == 0){
            $this->syPack->setCommandAndData(SyPack::COMMAND_TYPE_RPC_CLIENT_SEND_TASK_REQ, [
                'task_command' => Server::TASK_TYPE_CLEAR_LOCAL_USER_CACHE,
                'task_params' => [],
            ]);
            $apiTaskStr = $this->syPack->packData();
            $this->syPack->init();
            foreach ($data['projects'] as $eProject) {
                $this->sendSyTaskReq($eProject['host'], $eProject['port'], $apiTaskStr, 'rpc');
            }
        } else {
            $this->syPack->setCommandAndData(SyPack::COMMAND_TYPE_SOCKET_CLIENT_SEND_TASK_REQ, [
                'task_module' => $moduleTag,
                'task_command' => Server::TASK_TYPE_CLEAR_LOCAL_USER_CACHE,
                'task_params' => [],
            ]);
            $apiTaskStr = $this->syPack->packData();
            $this->syPack->init();
            foreach ($data['projects'] as $eProject) {
                $this->sendSyTaskReq($eProject['host'], $eProject['port'], $apiTaskStr, 'http');
            }
        }
    }
}