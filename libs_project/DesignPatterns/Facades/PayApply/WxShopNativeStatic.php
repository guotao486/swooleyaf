<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-5
 * Time: 上午12:44
 */
namespace DesignPatterns\Facades\PayApply;

use Constant\Project;
use DesignPatterns\Facades\PayApplyFacade;
use DesignPatterns\Factories\CacheSimpleFactory;
use Tool\Tool;
use Traits\SimpleFacadeTrait;
use Wx\Shop\PayNativePre;
use Wx\WxUtilShop;

class WxShopNativeStatic extends PayApplyFacade {
    use SimpleFacadeTrait;

    protected static function checkParams(array $data) : array {
        return [
            'a00_appid' => Tool::getConfig('project.' . SY_ENV . SY_PROJECT . '.wx.appid.default'),
        ];
    }

    protected static function apply(array $data) : array {
        $prePay = new PayNativePre($data['a00_appid']);
        $prePay->setProductId($data['content_result']['pay_sn']);
        $applyRes = WxUtilShop::applyPreNativePay($prePay);
        unset($prePay);

        $redisKey = Project::REDIS_PREFIX_WX_NATIVE_PRE . $data['content_result']['pay_sn'];
        CacheSimpleFactory::getRedisInstance()->hMset($redisKey, [
            'pay_name' => $data['content_result']['pay_name'],
            'pay_money' => $data['content_result']['pay_money'],
            'pay_attach' => $data['content_result']['pay_attach'],
            'pay_sn' => $data['content_result']['pay_sn'],
            'cache_key' => $redisKey,
        ]);
        CacheSimpleFactory::getRedisInstance()->expire($redisKey, 7200);

        return [
            'code_url' => $applyRes
        ];
    }
}