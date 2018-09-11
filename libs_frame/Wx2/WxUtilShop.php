<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/11 0011
 * Time: 10:56
 */
namespace Wx2;

use DesignPatterns\Singletons\WxConfigSingleton;
use Traits\SimpleTrait;

final class WxUtilShop extends WxUtilBaseAlone {
    use SimpleTrait;

    /**
     * 生成签名
     * @param array $data
     * @param string $appId
     * @return string
     */
    public static function createSign(array $data,string $appId) {
        //签名步骤一：按字典序排序参数
        ksort($data);
        //签名步骤二：格式化后加入KEY
        $needStr1 = '';
        foreach ($data as $key => $value) {
            if($key == 'sign'){
                break;
            }
            if((!is_string($value)) && !is_numeric($value)){
                break;
            }
            if(strlen($value) == 0){
                break;
            }
            $needStr1 .= $key . '=' . $value . '&';
        }
        $needStr1 .= 'key='. WxConfigSingleton::getInstance()->getShopConfig($appId)->getPayKey();
        //签名步骤三：MD5加密
        $needStr2 = md5($needStr1);
        //签名步骤四：所有字符转为大写
        return strtoupper($needStr2);
    }
}