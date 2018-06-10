<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/6/22 0022
 * Time: 12:25
 */
class MIndexController extends CommonController {
    public function init() {
        parent::init();
    }

    public function indexAction() {
        $this->SyResult->setData([
            'xxx' => 111,
            'aaa' => \Tool\Tool::getNowTime(),
        ]);

        $this->sendRsp();
    }

    public function getMPingAction() {
        $this->SyResult->setData([
            'xxx' => 222,
            'aaa' => \Tool\Tool::getNowTime(),
        ]);
        \Log\Log::info('mi222');

        $this->sendRsp();
    }
}