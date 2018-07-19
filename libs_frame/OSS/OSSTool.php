<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/7/19 0019
 * Time: 11:14
 */
namespace OSS;

use DesignPatterns\Singletons\OSSSingleton;
use Traits\SimpleTrait;

class OSSTool {
    use SimpleTrait;

    /**
     * 签名前端配置
     * @param array $policy
     * @return array
     */
    public static function signFrontPolicy(array $policy) : array {
        $policySign = base64_encode(json_encode($policy));
        $signature = base64_encode(hash_hmac('sha1', $policySign, OSSSingleton::getInstance()->getConfig()->getKeySecret(), true));

        return [
            'policy_sign' => $policySign,
            'signature' => $signature,
        ];
    }
}