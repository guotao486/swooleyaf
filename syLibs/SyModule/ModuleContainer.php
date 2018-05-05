<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/5/5 0005
 * Time: 9:34
 */
namespace SyModule;

use Constant\Server;
use Tool\BaseContainer;

class ModuleContainer extends BaseContainer {
    public function __construct(){
        $this->registryMap = [
            Server::MODULE_NAME_API,
            Server::MODULE_NAME_USER,
            Server::MODULE_NAME_ORDER,
            Server::MODULE_NAME_SERVICE,
        ];

        $this->bind(Server::MODULE_NAME_API, function () {
            return SyModuleApi::getInstance();
        });
        $this->bind(Server::MODULE_NAME_USER, function () {
            return SyModuleUser::getInstance();
        });
        $this->bind(Server::MODULE_NAME_ORDER, function () {
            return SyModuleOrder::getInstance();
        });
        $this->bind(Server::MODULE_NAME_SERVICE, function () {
            return SyModuleService::getInstance();
        });
    }
}