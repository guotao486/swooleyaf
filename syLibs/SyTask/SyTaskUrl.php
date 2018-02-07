<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 18-2-4
 * Time: 下午4:37
 */
namespace SyTask;

use Constant\Project;
use Tool\Cron\CronTool;

class SyTaskUrl {
    /**
     * 任务标识
     * @var string
     */
    private $tag = '';
    /**
     * 持久化类型
     * @var int
     */
    private $persist_type = 0;
    /**
     * 执行对象
     * @var string
     */
    private $exec_obj = '';
    /**
     * 执行参数
     * @var array
     */
    private $exec_params = [];
    /**
     * 执行时间
     * @var int|\Tool\Cron\CronData
     */
    private $exec_time = 0;

    public function __construct() {
        $this->persist_type = Project::TASK_PERSIST_TYPE_SINGLE;
    }

    private function __clone() {
    }

    /**
     * @return string
     */
    public function getTag() : string {
        return $this->tag;
    }

    /**
     * @param string $tag
     */
    public function setTag(string $tag) {
        $this->tag = $tag;
    }

    /**
     * @param int $persistType
     * @param mixed $execTime
     */
    public function setPersistTypeAndExecTime(int $persistType, $execTime) {
        if ($persistType == Project::TASK_PERSIST_TYPE_SINGLE) {
            $this->exec_time = (int)$execTime;
        } else if ($persistType == Project::TASK_PERSIST_TYPE_INTERVAL) {
            $this->exec_time = (int)$execTime;
        } else if ($persistType == Project::TASK_PERSIST_TYPE_CRON) {
            $this->exec_time = CronTool::analyseCron($execTime);
        }
        $this->persist_type = $persistType;
    }

    /**
     * @return int
     */
    public function getPersistType() : int {
        return $this->persist_type;
    }

    /**
     * @return int|\Tool\Cron\CronData
     */
    public function getExecTime() {
        return $this->exec_time;
    }

    /**
     * @param string $execObj
     */
    public function setExecObjAndParams(string $execObj,array $params) {
        $this->exec_obj = $execObj;
        $this->exec_params = $params;
        if (!empty($params)) {
            if (strpos($execObj, '?') === false) {
                $this->exec_obj .= '?';
            } else {
                $this->exec_obj .= '&';
            }
            $this->exec_obj .= http_build_query($execObj);
        }
    }

    /**
     * @return string
     */
    public function getExecObj() : string {
        return $this->exec_obj;
    }

    /**
     * @return array
     */
    public function getExecParams() : array {
        return $this->exec_params;
    }

    public function sendUrl() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->exec_obj);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 3000);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        $errorNo = curl_errno($ch);
        curl_close($ch);
        if ($errorNo == 0) {
            return $res;
        } else {
            return '{"code":' . $errorNo . ',"msg":"处理url请求失败"}';
        }
    }
}