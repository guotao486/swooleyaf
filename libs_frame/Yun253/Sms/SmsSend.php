<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/8 0008
 * Time: 15:30
 */
namespace Yun253\Sms;

use Constant\ErrorCode;
use DesignPatterns\Singletons\Yun253Singleton;
use Exception\Yun253\SmsException;
use Yun253\YunBase;

class SmsSend extends YunBase {
    /**
     * 接收手机号码列表
     * @var array
     */
    private $phoneList = [];
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
        $this->serviceUrl = Yun253Singleton::getInstance()->getCommonConfig()->getUrlSmsSend();
        $this->reqData['report'] = 'false';
        $this->reqData['sendtime'] = date('YmdHi');
    }

    private function __clone(){
    }

    /**
     * @param array $phoneList
     * @throws \Exception\Yun253\SmsException
     */
    public function setPhoneList(array $phoneList){
        if(empty($phoneList)){
            throw new SmsException('接收号码不能为空', ErrorCode::SMS_PARAM_ERROR);
        } else if(count($phoneList) > 200){
            throw new SmsException('接收号码不能超过200个', ErrorCode::SMS_PARAM_ERROR);
        }

        foreach ($phoneList as $ePhone) {
            if(ctype_digit($ePhone) && (strlen($ePhone) == 11) && ($ePhone{0} == '1')){
                $this->phoneList[$ePhone] = 1;
            } else {
                throw new SmsException('接收号码不合法', ErrorCode::SMS_PARAM_ERROR);
            }
        }
    }

    /**
     * @param string $phoneNum
     * @throws \Exception\Yun253\SmsException
     */
    public function addPhoneNum(string $phoneNum){
        if(count($this->phoneList) >= 200){
            throw new SmsException('接收号码不能超过200个', ErrorCode::SMS_PARAM_ERROR);
        }
        if(ctype_digit($phoneNum) && (strlen($phoneNum) == 11) && ($phoneNum{0} == '1')){
            $this->phoneList[$phoneNum] = 1;
        } else {
            throw new SmsException('接收号码不合法', ErrorCode::SMS_PARAM_ERROR);
        }
    }

    /**
     * @param string $signName
     * @param string $msg
     * @throws \Exception\Yun253\SmsException
     */
    public function setSignNameAndMsg(string $signName,string $msg){
        if(strlen($signName) == 0){
            throw new SmsException('签名名称不能为空', ErrorCode::SMS_PARAM_ERROR);
        } else if(strlen($msg) == 0){
            throw new SmsException('短信内容不能为空', ErrorCode::SMS_PARAM_ERROR);
        }

        $this->reqData['msg'] = '【' . $signName . '】' . $msg;
    }

    public function getDetail() : array {
        if (empty($this->phoneList)) {
            throw new SmsException('接收号码不能为空', ErrorCode::SMS_PARAM_ERROR);
        }
        if (!isset($this->reqData['msg'])) {
            throw new SmsException('短信内容不能为空', ErrorCode::SMS_PARAM_ERROR);
        }
        $this->reqData['phone'] = implode(',', array_keys($this->phoneList));

        return $this->getContent();
    }
}