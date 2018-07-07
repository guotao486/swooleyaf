<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/7/7 0007
 * Time: 16:44
 */
namespace SyIM;

use Constant\ErrorCode;
use DesignPatterns\Singletons\IMConfigSingleton;
use Exception\IM\TencentException;
use Traits\SimpleTrait;

class TencentIMTool {
    use SimpleTrait;

    /**
     * 生成签名
     * @param string $userTag 用户标识
     * @return string
     * @throws \Exception\IM\TencentException
     */
    public static function createSign(string $userTag){
        $sign = '';
        $commandStatus = 0;
        $command = IMConfigSingleton::getInstance()->getTencentConfig()->getCommandSign()
                   . ' ' . escapeshellarg(IMConfigSingleton::getInstance()->getTencentConfig()->getPrivateKey())
                   . ' ' . escapeshellarg(IMConfigSingleton::getInstance()->getTencentConfig()->getAppId())
                   . ' ' . escapeshellarg($userTag);
        exec($command, $sign, $commandStatus);
        if($commandStatus == -1){
            throw new TencentException('生成即时通讯签名失败', ErrorCode::IM_SIGN_ERROR);
        }

        return $sign;
    }
}