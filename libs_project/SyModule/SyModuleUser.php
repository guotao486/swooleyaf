<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-8-22
 * Time: 下午10:45
 */
namespace SyModule;

use Constant\Project;

class SyModuleUser extends ModuleRpc {
    /**
     * @var \SyModule\SyModuleUser
     */
    private static $instance = null;

    private function __construct() {
        $this->moduleName = Project::MODULE_BASE_USER;
        parent::init();
    }

    /**
     * @return \SyModule\SyModuleUser
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}