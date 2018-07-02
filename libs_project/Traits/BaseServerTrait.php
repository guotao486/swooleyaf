<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-5-27
 * Time: 上午10:09
 */
namespace Traits;

use Constant\ErrorCode;
use Constant\Project;
use DesignPatterns\Singletons\MysqlSingleton;
use DesignPatterns\Singletons\RedisSingleton;
use Response\Result;
use Tool\SyPack;
use Tool\Tool;

trait BaseServerTrait {
    /**
     * 配置数组
     * @var array
     */
    protected $_configs = [];
    /**
     * @var \Tool\SyPack
     */
    protected $_syPack = null;
    /**
     * 项目缓存列表
     * @var \swoole_table
     */
    protected static $_syProject = null;
    /**
     * 用户信息列表
     * @var \swoole_table
     */
    protected static $_syUsers = null;
    /**
     * 项目微信商户号token缓存列表
     * @var \swoole_table
     */
    protected static $_syWxShopToken = null;
    /**
     * 项目微信开放平台授权公众号token缓存列表
     * @var \swoole_table
     */
    protected static $_syWxOpenAuthorizerToken = null;
    /**
     * 最大用户数量
     * @var int
     */
    private static $_syUserMaxNum = 0;
    /**
     * 当前用户数量
     * @var int
     */
    private static $_syUserNowNum = 0;
    /**
     * 最大微信商户号token数量
     * @var int
     */
    private static $_syWxShopTokenMaxNum = 0;
    /**
     * 当前微信商户号token数量
     * @var int
     */
    private static $_syWxShopTokenNowNum = 0;
    /**
     * 最大微信开放平台授权公众号token数量
     * @var int
     */
    private static $_syWxOpenAuthorizerTokenMaxNum = 0;
    /**
     * 当前微信开放平台授权公众号token数量
     * @var int
     */
    private static $_syWxOpenAuthorizerTokenNowNum = 0;

    private function checkBaseServer() {
        self::$_syUserNowNum = 0;
        self::$_syUserMaxNum = (int)$this->_configs['server']['cachenum']['users'];
        if (self::$_syUserMaxNum < 2) {
            exit('用户信息缓存数量不能小于2');
        } else if ((self::$_syUserMaxNum & (self::$_syUserMaxNum - 1)) != 0) {
            exit('用户信息缓存数量必须是2的指数倍');
        }

        //检测redis服务是否启动
        RedisSingleton::getInstance()->checkConn();

        $this->_syPack = new SyPack();
    }

    /**
     * 获取项目缓存
     * @param string $key 键名
     * @param string $field 字段名
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function getProjectCache(string $key,string $field='', $default=null){
        $data = self::$_syProject->get($key);
        if($data === false){
            return $default;
        } else if($field === ''){
            return $data;
        } else {
            return $data[$field] ?? $default;
        }
    }

    /**
     * 设置项目缓存
     * @param string $key 键名
     * @param array $data 键值
     * @return bool
     */
    public static function setProjectCache(string $key,array $data) : bool {
        $trueKey = trim($key);
        if(strlen($trueKey) > 0){
            $data['tag'] = $trueKey;
            self::$_syProject->set($trueKey, $data);

            return true;
        } else {
            return false;
        }
    }

    /**
     * 添加本地用户信息
     * @param string $sessionId 会话ID
     * @param array $userData
     * @return bool
     */
    public static function addLocalUserInfo(string $sessionId,array $userData) : bool {
        if (self::$_syUsers->exist($sessionId)) {
            $userData['session_id'] = $sessionId;
            $userData['add_time'] = Tool::getNowTime();
            self::$_syUsers->set($sessionId, $userData);
            return true;
        } else if (self::$_syUserNowNum < self::$_syUserMaxNum) {
            $userData['session_id'] = $sessionId;
            $userData['add_time'] = Tool::getNowTime();
            self::$_syUsers->set($sessionId, $userData);
            self::$_syUserNowNum++;
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取本地用户信息
     * @param string $sessionId 会话ID
     * @return array
     */
    public static function getLocalUserInfo(string $sessionId){
        $data = self::$_syUsers->get($sessionId);
        return $data === false ? [] : $data;
    }

    /**
     * 删除本地用户信息
     * @param string $sessionId 会话ID
     * @return bool
     */
    public static function delLocalUserInfo(string $sessionId) {
        $delRes = self::$_syUsers->del($sessionId);
        if($delRes){
            self::$_syUserNowNum--;
        }

        return $delRes;
    }

    /**
     * 清理本地用户信息缓存
     */
    protected function clearLocalUsers() {
        $time = Tool::getNowTime() - Project::TIME_EXPIRE_LOCAL_USER_CACHE;
        $delKeys = [];
        foreach (self::$_syUsers as $eUser) {
            if($eUser['add_time'] <= $time){
                $delKeys[] = $eUser['session_id'];
            }
        }
        foreach ($delKeys as $eKey) {
            self::$_syUsers->del($eKey);
        }
        self::$_syUserNowNum = count(self::$_syUsers);
    }

    /**
     * 设置项目微信商户号token缓存
     * @param string $appId 公众号app id
     * @param array $data 键值
     * @return bool
     */
    public static function setWxShopTokenCache(string $appId,array $data) : bool {
        if(empty($data)){
            return false;
        } else if(self::$_syWxShopToken->exist($appId)){
            self::$_syWxShopToken->set($appId, $data);

            return true;
        } else if(self::$_syWxShopTokenNowNum < self::$_syWxShopTokenMaxNum){
            self::$_syWxShopToken->set($appId, $data);
            self::$_syWxShopTokenNowNum++;

            return true;
        } else {
            return true;
        }
    }

    /**
     * 获取项目微信商户号token缓存
     * @param string $appId 公众号app id
     * @param string $field 字段名
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function getWxShopTokenCache(string $appId,string $field='', $default=null){
        $data = self::$_syWxShopToken->get($appId);
        if($data === false){
            return $default;
        } else if($field === ''){
            return $data;
        } else {
            return $data[$field] ?? $default;
        }
    }

    protected function clearLocalWxShopTokens() {
        $nowTime = Tool::getNowTime();
        $delKeys = [];
        foreach (self::$_syWxShopToken as $eToken) {
            if($eToken['clear_time'] < $nowTime){
                $delKeys[] = $eToken['app_id'];
            }
        }
        foreach ($delKeys as $eKey) {
            self::$_syWxShopToken->del($eKey);
        }
        self::$_syWxShopTokenNowNum = count(self::$_syWxShopToken);
    }

    /**
     * 设置项目微信开放平台授权公众号token缓存
     * @param string $appId 公众号app id
     * @param array $data 键值
     * @return bool
     */
    public static function setWxOpenAuthorizerTokenCache(string $appId,array $data) : bool {
        if(empty($data)){
            return false;
        } else if(self::$_syWxOpenAuthorizerToken->exist($appId)){
            self::$_syWxOpenAuthorizerToken->set($appId, $data);

            return true;
        } else if(self::$_syWxOpenAuthorizerTokenNowNum < self::$_syWxOpenAuthorizerTokenMaxNum){
            self::$_syWxOpenAuthorizerToken->set($appId, $data);
            self::$_syWxOpenAuthorizerTokenNowNum++;

            return true;
        } else {
            return true;
        }
    }

    /**
     * 获取项目微信开放平台授权公众号token缓存
     * @param string $appId 公众号app id
     * @param string $field 字段名
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function getWxOpenAuthorizerTokenCache(string $appId,string $field='', $default=null){
        $data = self::$_syWxOpenAuthorizerToken->get($appId);
        if($data === false){
            return $default;
        } else if($field === ''){
            return $data;
        } else {
            return $data[$field] ?? $default;
        }
    }

    protected function clearLocalWxOpenAuthorizerTokens() {
        $nowTime = Tool::getNowTime();
        $delKeys = [];
        foreach (self::$_syWxOpenAuthorizerToken as $eToken) {
            if($eToken['clear_time'] < $nowTime){
                $delKeys[] = $eToken['app_id'];
            }
        }
        foreach ($delKeys as $eKey) {
            self::$_syWxOpenAuthorizerToken->del($eKey);
        }
        self::$_syWxOpenAuthorizerTokenNowNum = count(self::$_syWxOpenAuthorizerToken);
    }

    protected function initTableByBaseStart() {
        self::$_syProject = new \swoole_table((int)$this->_configs['server']['cachenum']['local']);
        self::$_syProject->column('tag', \swoole_table::TYPE_STRING, 64);
        self::$_syProject->column('value', \swoole_table::TYPE_STRING, 200);
        self::$_syProject->column('expire_time', \swoole_table::TYPE_INT, 4);
        self::$_syProject->create();

        self::$_syUsers = new \swoole_table(self::$_syUserMaxNum);
        self::$_syUsers->column('session_id', \swoole_table::TYPE_STRING, 16);
        self::$_syUsers->column('user_id', \swoole_table::TYPE_STRING, 32);
        self::$_syUsers->column('user_name', \swoole_table::TYPE_STRING, 64);
        self::$_syUsers->column('user_headimage', \swoole_table::TYPE_STRING, 255);
        self::$_syUsers->column('user_openid', \swoole_table::TYPE_STRING, 32);
        self::$_syUsers->column('user_unid', \swoole_table::TYPE_STRING, 32);
        self::$_syUsers->column('user_phone', \swoole_table::TYPE_STRING, 11);
        self::$_syUsers->column('add_time', \swoole_table::TYPE_INT, 4);
        self::$_syUsers->create();

        self::$_syWxShopToken = new \swoole_table((int)$this->_configs['server']['cachenum']['wxshop']['token']);
        self::$_syWxShopToken->column('app_id', \swoole_table::TYPE_STRING, 18);
        self::$_syWxShopToken->column('access_token', \swoole_table::TYPE_STRING, 200);
        self::$_syWxShopToken->column('js_ticket', \swoole_table::TYPE_STRING, 200);
        self::$_syWxShopToken->column('expire_time', \swoole_table::TYPE_INT, 4);
        self::$_syWxShopToken->column('clear_time', \swoole_table::TYPE_INT, 4);
        self::$_syWxShopToken->create();

        self::$_syWxOpenAuthorizerToken = new \swoole_table((int)$this->_configs['server']['cachenum']['wxopen']['authorizertoken']);
        self::$_syWxOpenAuthorizerToken->column('app_id', \swoole_table::TYPE_STRING, 18);
        self::$_syWxOpenAuthorizerToken->column('access_token', \swoole_table::TYPE_STRING, 200);
        self::$_syWxOpenAuthorizerToken->column('js_ticket', \swoole_table::TYPE_STRING, 200);
        self::$_syWxOpenAuthorizerToken->column('expire_time', \swoole_table::TYPE_INT, 4);
        self::$_syWxOpenAuthorizerToken->column('clear_time', \swoole_table::TYPE_INT, 4);
        self::$_syWxOpenAuthorizerToken->create();
    }

    protected function handleBaseTask(\swoole_server $server,int $taskId,int $fromId,string $data) {
        $result = new Result();
        if(!$this->_syPack->unpackData($data)){
            $result->setCodeMsg(ErrorCode::COMMON_PARAM_ERROR, '数据格式不合法');
            return $result->getJson();
        }

        RedisSingleton::getInstance()->reConnect();
        if(SY_RECONNECT_DB){
            MysqlSingleton::getInstance()->reConnect();
        }

        $command = $this->_syPack->getCommand();
        $commandData = $this->_syPack->getData();
        $this->_syPack->init();

        if(in_array($command, [SyPack::COMMAND_TYPE_SOCKET_CLIENT_SEND_TASK_REQ, SyPack::COMMAND_TYPE_RPC_CLIENT_SEND_TASK_REQ])){
            $taskCommand = Tool::getArrayVal($commandData, 'task_command', '');
            switch ($taskCommand) {
                case Project::TASK_TYPE_CLEAR_LOCAL_USER_CACHE:
                    $this->clearLocalUsers();
                    break;
                case Project::TASK_TYPE_CLEAR_LOCAL_WXSHOP_TOKEN_CACHE:
                    $this->clearLocalWxShopTokens();
                    break;
                case Project::TASK_TYPE_CLEAR_LOCAL_WXOPEN_AUTHORIZER_TOKEN_CACHE:
                    $this->clearLocalWxOpenAuthorizerTokens();
                    break;
                default:
                    return [
                        'command' => $command,
                        'params' => $commandData,
                    ];
                    break;
            }

            $result->setData([
                'result' => 'success',
            ]);
        } else {
            $result->setData([
                'result' => 'fail',
            ]);
        }

        return $result->getJson();
    }
}