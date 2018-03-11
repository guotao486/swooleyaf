<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 18-3-10
 * Time: 下午4:32
 */
namespace Dao;

use AliPay\AliPayUtil;
use AliPay\PayQrCode;
use AliPay\PayWap;
use Constant\ErrorCode;
use Constant\Server;
use DesignPatterns\Factories\CacheSimpleFactory;
use Exception\Common\CheckException;
use Factories\SyBaseMysqlFactory;
use Interfaces\Containers\PayContainer;
use Log\Log;
use Request\SyRequest;
use Tool\SyUser;
use Tool\Tool;
use Traits\SimpleDaoTrait;
use Wx\PayNativePre;
use Wx\UnifiedOrder;
use Wx\WxUtil;

class PayDao {
    use SimpleDaoTrait;

    private static $payTypeCheckMap = [
        'a000' => 'payTypeCheckWxJs',
        'a001' => 'payTypeCheckWxNativeDynamic',
        'a002' => 'payTypeCheckWxNativeStatic',
        'a100' => 'payTypeCheckAliCode',
        'a101' => 'payTypeCheckAliWeb',
    ];
    private static $payTypeHandleMap = [
        'a000' => 'payTypeHandleWxJs',
        'a001' => 'payTypeHandleWxNativeDynamic',
        'a002' => 'payTypeHandleWxNativeStatic',
        'a100' => 'payTypeHandleAliCode',
        'a101' => 'payTypeHandleAliWeb',
    ];

    /**
     * @var \Interfaces\Containers\PayContainer
     */
    private static $payContainer = null;

    /**
     * @param string $payContent
     * @return \Interfaces\PayService|null
     */
    private static function getPayService(string $payContent) {
        if (is_null(self::$payContainer)) {
            self::$payContainer = new PayContainer();
        }

        return self::$payContainer->getObj($payContent);
    }

    private static function payTypeCheckWxJs(array &$data) {
        $wxOpenid = SyUser::getOpenId();
        if (strlen($wxOpenid) == 0) {
            throw new CheckException('请先微信登录', ErrorCode::USER_NOT_LOGIN_WX_AUTH);
        }

        $data['a00_openid'] = $wxOpenid;
        $data['a00_appid'] = Tool::getConfig('project.' . SY_ENV . SY_PROJECT . '.wx.appid.default');
    }

    private static function payTypeCheckWxNativeDynamic(array &$data) {
        $data['a00_appid'] = Tool::getConfig('project.' . SY_ENV . SY_PROJECT . '.wx.appid.default');
    }

    private static function payTypeCheckWxNativeStatic(array &$data) {
        $data['a00_appid'] = Tool::getConfig('project.' . SY_ENV . SY_PROJECT . '.wx.appid.default');
    }

    private static function payTypeCheckAliCode(array &$data) {
        $data['a01_timeout'] = (string)SyRequest::getParams('a01_timeout', '');
    }

    private static function payTypeCheckAliWeb(array &$data) {
        $returnUrl = (string)SyRequest::getParams('a01_returnurl', '');
        if(strlen($returnUrl) == 0){
            throw new CheckException('同步通知链接不能为空', ErrorCode::COMMON_PARAM_ERROR);
        }

        $data['a01_timeout'] = (string)SyRequest::getParams('a01_timeout', '');
        $data['a01_returnurl'] = $returnUrl;
    }

    private static function payTypeHandleWxJs(array $data) {
        $order = new UnifiedOrder(UnifiedOrder::TRADE_TYPE_JSAPI, $data['a00_appid']);
        $order->setBody($data['content_result']['pay_name']);
        $order->setTotalFee($data['content_result']['pay_money']);
        $order->setOutTradeNo($data['content_result']['pay_sn']);
        $order->setAttach($data['content_result']['pay_attach']);
        $order->setOpenid($data['a00_openid']);
        $applyRes = WxUtil::applyJsPay($order, 'shop');
        unset($order);
        if($applyRes['code'] > 0){
            throw new CheckException($applyRes['message'], ErrorCode::COMMON_PARAM_ERROR);
        }

        return [
            'config' => $applyRes['data']['config'],
            'api' => $applyRes['data']['pay'],
        ];
    }

    private static function payTypeHandleWxNativeDynamic(array $data) {
        $order = new UnifiedOrder(UnifiedOrder::TRADE_TYPE_NATIVE, $data['a00_appid']);
        $order->setBody($data['content_result']['pay_name']);
        $order->setTotalFee($data['content_result']['pay_money']);
        $order->setOutTradeNo($data['content_result']['pay_sn']);
        $order->setAttach($data['content_result']['pay_attach']);
        $applyRes = WxUtil::applyNativePay($order);
        unset($order);
        if($applyRes['code'] > 0){
            throw new CheckException($applyRes['message'], ErrorCode::COMMON_PARAM_ERROR);
        }

        return [
            'code_url' => $applyRes['data']['code_url']
        ];
    }

    private static function payTypeHandleWxNativeStatic(array $data) {
        $prePay = new PayNativePre($data['a00_appid']);
        $prePay->setProductId($data['content_result']['pay_sn']);
        $applyRes = WxUtil::applyPreNativePay($prePay);
        unset($prePay);

        $redisKey = Server::REDIS_PREFIX_WX_NATIVE_PRE . $data['content_result']['pay_sn'];
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

    private static function payTypeHandleAliCode(array $data) {
        $pay = new PayQrCode();
        $pay->setSubject($data['content_result']['pay_name']);
        $pay->setTotalAmount($data['content_result']['pay_money']);
        $pay->setAttach($data['content_result']['pay_attach']);
        $pay->setTimeoutExpress($data['a01_timeout']);
        $pay->setOutTradeNo($data['content_result']['pay_sn']);
        $payRes = AliPayUtil::applyQrCodePay($pay);
        unset($pay);
        if ($payRes['code'] > 0) {
            throw new CheckException($payRes['message'], ErrorCode::COMMON_PARAM_ERROR);
        }

        return [
            'qr_code' => $payRes['data']['qr_code'],
        ];
    }

    private static function payTypeHandleAliWeb(array $data) {
        $pay = new PayWap();
        $pay->setReturnUrl($data['a01_returnurl']);
        $pay->setSubject($data['content_result']['pay_name']);
        $pay->setTotalAmount($data['content_result']['pay_money']);
        $pay->setAttach($data['content_result']['pay_attach']);
        $pay->setTimeoutExpress($data['a01_timeout']);
        $pay->setOutTradeNo($data['content_result']['pay_sn']);
        $html = AliPayUtil::createWapPayHtml($pay);
        unset($pay);

        return [
            'html' => $html,
        ];
    }

    public static function applyPay(array $data) {
        $typeCheckFunc = Tool::getArrayVal(self::$payTypeCheckMap, $data['pay_type'], null);
        if (is_null($typeCheckFunc)) {
            throw new CheckException('支付类型不支持', ErrorCode::COMMON_PARAM_ERROR);
        }
        self::$typeCheckFunc($data);

        $payService = self::getPayService($data['pay_content']);
        if (is_null($payService)) {
            throw new CheckException('支付内容不支持', ErrorCode::COMMON_PARAM_ERROR);
        }
        $contentParams = $payService->checkPayParams();
        $data['content_result'] = $payService->getPayInfo($contentParams);

        $typeHandleFunc = Tool::getArrayVal(self::$payTypeHandleMap, $data['pay_type'], null);
        return self::$typeHandleFunc($data);
    }

    public static function completePay(array $data){
        //添加支付原始记录
        $payHistory = SyBaseMysqlFactory::PayHistoryEntity();
        $payHistory->type = $data['pay_type'];
        $payHistory->trade_sn = $data['pay_tradesn'];
        $payHistory->seller_sn = $data['pay_sellersn'];
        $payHistory->app_id = $data['pay_appid'];
        $payHistory->buyer_id = $data['pay_buyerid'];
        $payHistory->money = $data['pay_money'];
        $payHistory->attach = $data['pay_attach'];
        $payHistory->content = Tool::jsonEncode($data['pay_data'], JSON_UNESCAPED_UNICODE);
        $payHistory->status = $data['pay_status'];
        $payHistory->created = time();
        $ormResult1 = $payHistory->getContainer()->getModel()->getOrmDbTable();
        $ormResult1->where('`seller_sn`=?', [$data['pay_sellersn']]);
        $historyInfo = $payHistory->getContainer()->getModel()->findOne($ormResult1);
        if (empty($historyInfo)) {
            $payHistory->getContainer()->getModel()->insert($payHistory->getEntityDataArray());
        }

        $payContent = substr($data['pay_sellersn'], 0, 4);
        $payService = self::getPayService($payContent);
        if(is_null($payService)){
            throw new CheckException('支付内容不支持', ErrorCode::COMMON_PARAM_ERROR);
        }

        $successRes = [];
        try {
            $payHistory->getContainer()->getModel()->openTransaction();
            $successRes = $payService->handlePaySuccess($data);
            $payHistory->getContainer()->getModel()->commitTransaction();
        } catch (\Exception $e) {
            $payHistory->getContainer()->getModel()->rollbackTransaction();
            Log::error($e->getMessage(), $e->getCode(), $e->getTraceAsString());

            throw new CheckException('支付处理失败', ErrorCode::COMMON_SERVER_ERROR);
        } finally {
            unset($ormResult1, $payHistory);
            if(!empty($successRes)){
                $payService->handlePaySuccessAttach($successRes);
            }
        }
    }
}