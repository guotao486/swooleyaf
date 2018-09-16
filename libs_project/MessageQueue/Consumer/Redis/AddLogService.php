<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-8-23
 * Time: 下午10:05
 */
namespace MessageQueue\Consumer\Redis;

use Constant\Project;
use Log\Log;
use SyMessageQueue\ConsumerBase;
use SyMessageQueue\ConsumerInterface;
use Tool\Tool;

class AddLogService extends ConsumerBase implements ConsumerInterface {
    public function __construct() {
        parent::__construct();
        $this->topic = Project::MESSAGE_QUEUE_TOPIC_ADD_LOG;
    }

    private function __clone() {
    }

    public function handleMessage(array $data) {
        Log::info('mqdata:' . Tool::jsonEncode($data, JSON_UNESCAPED_UNICODE));
    }
}