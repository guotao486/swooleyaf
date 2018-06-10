<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 18-2-4
 * Time: 上午11:51
 */
namespace Dao;

use Constant\ErrorCode;
use Constant\Project;
use Entities\SyTask\TaskBaseEntity;
use Entities\SyTask\TaskLogEntity;
use Exception\Common\CheckException;
use Request\SyRequest;
use SyTask\SyTaskUrl;
use Tool\Cron\CronTool;
use Tool\Tool;
use Traits\SimpleDaoTrait;

class TaskDao {
    use SimpleDaoTrait;

    public static $totalStatus = [
        Project::TASK_STATUS_DELETE => '已删除',
        Project::TASK_STATUS_INVALID => '无效',
        Project::TASK_STATUS_VALID => '有效',
    ];
    public static $totalPersistTypes = [
        Project::TASK_PERSIST_TYPE_SINGLE => '单次任务',
        Project::TASK_PERSIST_TYPE_INTERVAL => '间隔任务',
        Project::TASK_PERSIST_TYPE_CRON => 'cron任务',
    ];

    /**
     * 一次性定时任务刷新时间
     * @var int
     */
    private static $singleRefreshTime = 0;
    /**
     * 一次性定时任务数组
     * @var array
     */
    private static $singleTasks = [];
    /**
     * 间隔定时任务刷新时间
     * @var int
     */
    private static $intervalRefreshTime = 0;
    /**
     * 间隔定时任务数组
     * @var array
     */
    private static $intervalTasks = [];
    /**
     * cron定时任务刷新时间
     * @var int
     */
    private static $cronRefreshTime = 0;
    /**
     * cron定时任务数组
     * @var array
     */
    private static $cronTasks = [];
    private static $addTaskMap = [
        Project::TASK_PERSIST_TYPE_SINGLE => 'addSingleTask',
        Project::TASK_PERSIST_TYPE_INTERVAL => 'addIntervalTask',
        Project::TASK_PERSIST_TYPE_CRON => 'addCronTask',
    ];

    private static function getSingleTasks() {
        $nowTime = Tool::getNowTime();
        if (($nowTime - self::$singleRefreshTime) >= 180) {
            $taskBase = new TaskBaseEntity();
            $ormResult1 = $taskBase->getContainer()->getModel()->getOrmDbTable();
            $ormResult1->where('`persist_type`=? AND `status`=? AND `start_time`<=?', [
                Project::TASK_PERSIST_TYPE_SINGLE,
                Project::TASK_STATUS_VALID,
                ($nowTime + 400),
            ])->order('`id` ASC');
            $taskList = $taskBase->getContainer()->getModel()->select($ormResult1, 1, 500);
            unset($ormResult1, $taskBase);
            self::$singleTasks = [];
            foreach ($taskList as $eTask) {
                $task = new SyTaskUrl();
                $task->setTag($eTask['tag']);
                $task->setExecObjAndParams($eTask['exec_obj'], Tool::jsonDecode($eTask['exec_params']));
                $task->setPersistTypeAndExecTime((int)$eTask['persist_type'], $eTask['exec_time']);
                self::$singleTasks[$eTask['tag']] = $task;
            }

            self::$singleRefreshTime = $nowTime;
        }

        return self::$singleTasks;
    }

    private static function getIntervalTasks() {
        $nowTime = Tool::getNowTime();
        if (($nowTime - self::$intervalRefreshTime) >= 300) {
            $taskBase = new TaskBaseEntity();
            $ormResult1 = $taskBase->getContainer()->getModel()->getOrmDbTable();
            $ormResult1->where('`persist_type`=? AND `status`=?', [
                Project::TASK_PERSIST_TYPE_INTERVAL,
                Project::TASK_STATUS_VALID,
            ])->order('`id` ASC');
            $taskList = $taskBase->getContainer()->getModel()->select($ormResult1);
            unset($ormResult1, $taskBase);
            self::$intervalTasks = [];
            foreach ($taskList as $eTask) {
                $task = new SyTaskUrl();
                $task->setTag($eTask['tag']);
                $task->setExecObjAndParams($eTask['exec_obj'], Tool::jsonDecode($eTask['exec_params']));
                $task->setPersistTypeAndExecTime((int)$eTask['persist_type'], $eTask['exec_time']);
                self::$intervalTasks[$eTask['tag']] = $task;
            }

            self::$intervalRefreshTime = $nowTime;
        }

        return self::$intervalTasks;
    }

    private static function getCronTasks() {
        $nowTime = Tool::getNowTime();
        if (($nowTime - self::$cronRefreshTime) >= 300) {
            $taskBase = new TaskBaseEntity();
            $ormResult1 = $taskBase->getContainer()->getModel()->getOrmDbTable();
            $ormResult1->where('`persist_type`=? AND `status`=?', [
                Project::TASK_PERSIST_TYPE_CRON,
                Project::TASK_STATUS_VALID,
            ])->order('`id` ASC');
            $taskList = $taskBase->getContainer()->getModel()->select($ormResult1);
            unset($ormResult1, $taskBase);
            self::$cronTasks = [];
            foreach ($taskList as $eTask) {
                $task = new SyTaskUrl();
                $task->setTag($eTask['tag']);
                $task->setExecObjAndParams($eTask['exec_obj'], Tool::jsonDecode($eTask['exec_params']));
                $task->setPersistTypeAndExecTime((int)$eTask['persist_type'], $eTask['exec_time']);
                self::$cronTasks[$eTask['tag']] = $task;
            }

            self::$cronRefreshTime = $nowTime;
        }

        return self::$cronTasks;
    }

    private static function addSingleTask(array &$data) {
        $time = (string)SyRequest::getParams('task_time');
        if (!is_numeric($time)) {
            throw new CheckException('任务时间不合法', ErrorCode::COMMON_PARAM_ERROR);
        }

        $nowTime = Tool::getNowTime();
        $taskTime = (int)$time;
        if ($taskTime <= $nowTime) {
            throw new CheckException('执行时间必须大于当前时间', ErrorCode::COMMON_PARAM_ERROR);
        } else if ($taskTime > ($nowTime + 31536000)) {
            throw new CheckException('执行时间不能超过当前时间一年', ErrorCode::COMMON_PARAM_ERROR);
        }

        $data['task_time'] = $taskTime;
        $data['start_time'] = $taskTime;
    }

    private static function addIntervalTask(array &$data) {
        $time = (string)SyRequest::getParams('task_time');
        if (!is_numeric($time)) {
            throw new CheckException('任务时间不合法', ErrorCode::COMMON_PARAM_ERROR);
        }

        $taskTime = (int)$time;
        if ($taskTime <= 0) {
            throw new CheckException('间隔时间必须大于0', ErrorCode::TIMER_PARAM_ERROR);
        } else if ($taskTime > 31536000) {
            throw new CheckException('间隔时间不能大于一年', ErrorCode::TIMER_PARAM_ERROR);
        }

        $data['task_time'] = $taskTime;
        $data['start_time'] = $taskTime;
    }

    private static function addCronTask(array &$data) {
        $timeStr = str_replace('/\s+/', ' ', trim(SyRequest::getParams('task_time')));
        if (strlen($timeStr) == 0) {
            throw new CheckException('任务时间不合法', ErrorCode::TIMER_PARAM_ERROR);
        } else if (preg_match('/^(\s(\*|\d+(\,\d+)*|\d+\-\d+(\,\d+\-\d+)*)(\/\d+){0,1}){6}$/', ' ' . $timeStr) == 0) {
            throw new CheckException('cron格式不合法', ErrorCode::COMMON_PARAM_ERROR);
        }
        CronTool::analyseCron($timeStr);

        $data['task_time'] = $timeStr;
        $data['start_time'] = Tool::getNowTime();
    }

    public static function addTask(array $data) {
        $funcName = Tool::getArrayVal(self::$addTaskMap, $data['persist_type'], null);
        if (is_null($funcName)) {
            throw new CheckException('持久化类型不支持', ErrorCode::COMMON_PARAM_ERROR);
        }
        self::$funcName($data);

        $nowTime = Tool::getNowTime();
        $taskTag = Tool::createNonceStr(6) . $nowTime;
        $taskBase = new TaskBaseEntity();
        $taskBase->tag = $taskTag;
        $taskBase->task_title = $data['task_title'];
        $taskBase->task_desc = $data['task_desc'];
        $taskBase->persist_type = $data['persist_type'];
        $taskBase->exec_obj = $data['task_url'];
        $taskBase->exec_params = Tool::jsonEncode($data['task_params'], JSON_UNESCAPED_UNICODE);
        $taskBase->exec_time = $data['task_time'];
        $taskBase->start_time = $data['start_time'];
        $taskBase->status = Project::TASK_STATUS_VALID;
        $taskBase->created = $nowTime;
        $taskBase->updated = $nowTime;
        $taskId = $taskBase->getContainer()->getModel()->insert($taskBase->getEntityDataArray());
        unset($taskBase);
        if (!$taskId) {
            throw new CheckException('添加任务失败', ErrorCode::COMMON_SERVER_ERROR);
        }

        return [
            'task_id' => $taskId,
            'task_tag' => $taskTag,
        ];
    }

    public static function delTask(array $data) {
        $taskBase = new TaskBaseEntity();
        $ormResult1 = $taskBase->getContainer()->getModel()->getOrmDbTable();
        $ormResult1->where('`tag`=?', [$data['task_tag']]);
        $effectNum = $taskBase->getContainer()->getModel()->update($ormResult1, [
            'status' => Project::TASK_STATUS_DELETE,
            'updated' => Tool::getNowTime(),
        ]);
        unset($ormResult1, $taskBase);

        return [
            'del_num' => $effectNum,
        ];
    }

    public static function getTaskList(array $data) {
        $taskBase = new TaskBaseEntity();
        $ormResult1 = $taskBase->getContainer()->getModel()->getOrmDbTable();
        if ($data['persist_type'] > 0) {
            $ormResult1->where('`persist_type`=?', [$data['persist_type']]);
        }
        if ($data['task_status'] > -2) {
            $ormResult1->where('`status`=?', [$data['task_status']]);
        }
        $ormResult1->order('`created` DESC,`id` DESC');
        $taskList = $taskBase->getContainer()->getModel()->findPage($ormResult1, $data['page'], $data['limit']);
        unset($ormResult1, $taskBase);

        $taskList['total_status'] = self::$totalStatus;
        $taskList['total_persisttypes'] = self::$totalPersistTypes;

        return $taskList;
    }

    public static function getTaskLogList(array $data) {
        $taskLog = new TaskLogEntity();
        $ormResult1 = $taskLog->getContainer()->getModel()->getOrmDbTable();
        $ormResult1->where('`tag`=?', [$data['task_tag']])->order('`created` DESC,`id` DESC');
        $logList = $taskLog->getContainer()->getModel()->findPage($ormResult1, $data['page'], $data['limit']);
        unset($ormResult1, $taskLog);

        return $logList;
    }

    public static function handleSingleTask(array $data) {
        $nowTime = Tool::getNowTime();
        $taskBase = new TaskBaseEntity();
        $taskLog = new TaskLogEntity();
        $tasks = self::getSingleTasks();
        foreach ($tasks as $taskTag => $task) {
            if ($task->getExecTime() <= $nowTime) {
                $ormResult1 = $taskBase->getContainer()->getModel()->getOrmDbTable();
                $ormResult1->where('`tag`=? AND `status`=?', [$taskTag, Project::TASK_STATUS_VALID]);
                $effectNum = $taskBase->getContainer()->getModel()->update($ormResult1, [
                    'status' => Project::TASK_STATUS_INVALID,
                    'updated' => $nowTime,
                ]);
                if ($effectNum) {
                    $taskLog->getContainer()->getModel()->insert([
                        'tag' => $taskTag,
                        'exec_result' => $task->sendUrl(),
                        'created' => $nowTime,
                    ]);
                }
                unset(self::$singleTasks[$taskTag]);
            }
        }
        unset($ormResult1, $taskBase, $taskLog, $tasks);

        return [
            'msg' => '执行一次性任务成功',
        ];
    }

    public static function handlePersistIntervalTask(array $data) {
        $nowTime = Tool::getNowTime();
        $taskLog = new TaskLogEntity();
        $tasks = self::getIntervalTasks();
        foreach ($tasks as $taskTag => $task) {
            if (($nowTime % $task->getExecTime()) == 0) {
                $taskLog->getContainer()->getModel()->insert([
                    'tag' => $taskTag,
                    'exec_result' => $task->sendUrl(),
                    'created' => $nowTime,
                ]);
            }
        }
        unset($taskLog, $tasks);

        return [
            'msg' => '执行间隔任务成功',
        ];
    }

    public static function handlePersistCronTask(array $data) {
        $nowTime = Tool::getNowTime();
        $timeData = explode('-', date('s-i-G-j-n-w', $nowTime));
        $timeArr = [
            'second' => (int)$timeData[0],
            'minute' => (int)$timeData[1],
            'hour' => (int)$timeData[2],
            'day' => (int)$timeData[3],
            'month' => (int)$timeData[4],
            'week' => (int)$timeData[5],
        ];
        $taskLog = new TaskLogEntity();
        $tasks = self::getIntervalTasks();
        foreach ($tasks as $taskTag => $task) {
            if ($task->getExecTime()->checkTime($timeArr)) {
                $taskLog->getContainer()->getModel()->insert([
                    'tag' => $taskTag,
                    'exec_result' => $task->sendUrl(),
                    'created' => $nowTime,
                ]);
            }
        }
        unset($taskLog, $tasks);

        return [
            'msg' => '执行cron任务成功',
        ];
    }
}