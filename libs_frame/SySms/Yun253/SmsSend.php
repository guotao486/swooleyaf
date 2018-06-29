<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/12/4 0004
 * Time: 11:34
 */
namespace SySms\Yun253;

use Constant\ErrorCode;
use Exception\Sms\Yun253Exception;

class SmsSend extends SmsBase {
    /**
     * 接收手机号码
     * @var string
     */
    private $phone = '';
    /**
     * 签名名称
     * @var string
     */
    private $signName = '';
    /**
     * 短信内容
     * @var string
     */
    private $msg = '';
    /**
     * 发送短信时间
     * @var string
     */
    private $sendTime = '';
    /**
     * 是否需要状态报告
     * @var string
     */
    private $report = '';

    public function __construct() {
        parent::__construct();
        $this->report = 'false';
        $this->sendTime = date('YmdHi');
    }

    private function __clone(){
    }

    /**
     * @return string
     */
    public function getPhone() : string {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @throws \Exception\Sms\Yun253Exception
     */
    public function setPhone(string $phone){
        if (preg_match('/^(\,1\d{10}){1,200}$/', ',' . $phone) > 0) {
            $this->phone = $phone;
        } else {
            throw new Yun253Exception('接收号码不合法', ErrorCode::SMS_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getSignName() : string {
        return $this->signName;
    }

    /**
     * @param string $signName
     * @throws \Exception\Sms\Yun253Exception
     */
    public function setSignName(string $signName){
        if (strlen($signName) > 0) {
            $this->signName = $signName;
        } else {
            throw new Yun253Exception('签名名称不能为空', ErrorCode::SMS_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getMsg() : string {
        return $this->msg;
    }

    /**
     * @param string $msg
     * @throws \Exception\Sms\Yun253Exception
     */
    public function setMsg(string $msg){
        if (strlen($msg) > 0) {
            $this->msg = $msg;
        } else {
            throw new Yun253Exception('短信内容不能为空', ErrorCode::SMS_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if (strlen($this->phone) == 0) {
            throw new Yun253Exception('接收号码不能为空', ErrorCode::SMS_PARAM_ERROR);
        }
        if (strlen($this->signName) == 0) {
            throw new Yun253Exception('签名名称不能为空', ErrorCode::SMS_PARAM_ERROR);
        }
        if (strlen($this->msg) == 0) {
            throw new Yun253Exception('短信内容不能为空', ErrorCode::SMS_PARAM_ERROR);
        }

        $resArr = $this->getBaseDetail();
        $resArr['msg'] = '【' . $this->signName . '】' . $this->msg;
        $resArr['phone'] = $this->phone;
        $resArr['sendtime'] = $this->sendTime;
        $resArr['report'] = $this->report;

        return $resArr;
    }
}