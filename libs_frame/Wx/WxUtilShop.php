<?php
/**
 * 微信公共类
 * User: 姜伟
 * Date: 2017/1/21 0021
 * Time: 9:05
 */
namespace Wx;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Tool\Tool;
use Traits\SimpleTrait;
use Wx\Shop\JsConfig;
use Wx\Shop\JsPayConfig;
use Wx\Shop\Menu;
use Wx\Shop\UnifiedOrder;

final class WxUtilShop extends WxUtilAloneBase {
    use SimpleTrait;

    private static $urlUnifiedOrder = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    private static $urlQrCode = 'http://paysdk.weixin.qq.com/example/qrcode.php?data=';
    private static $urlGetMenu = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=';
    private static $urlCreateMenu = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=';
    private static $urlDeleteMenu = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=';
    private static $urlDownloadMedia = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=';

    /**
     * 数组格式化成url参数
     * @param array $data
     * @return string
     */
    private static function arrayToUrlParams(array $data) {
        $buff = '';
        foreach ($data as $key => $value) {
            if (($key != 'sign') && (!is_array($value)) && (strlen($value . '') > 0)) {
                $buff .= $key . '=' . $value . '&';
            }
        }

        return trim($buff, '&');
    }

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
        $needStr1 = self::arrayToUrlParams($data) . '&key='. WxConfigSingleton::getInstance()->getShopConfig($appId)->getPayKey();
        //签名步骤三：MD5加密
        $needStr2 = md5($needStr1);
        //签名步骤四：所有字符转为大写
        return strtoupper($needStr2);
    }

    /**
     * 发起jsapi支付
     * @param \Wx\Shop\UnifiedOrder $order 订单信息
     * @param string $platType 平台类型
     *   shop:公众号
     *   mini:小程序
     *   openshop:第三方平台代理公众号
     *   openmini:第三方平台代理小程序
     * @return array
     */
    public static function applyJsPay(UnifiedOrder $order,string $platType) : array {
        $resArr = [
            'code' => 0,
        ];

        //发起统一下单
        $orderDetail = $order->getDetail();
        $reqXml = Tool::arrayToXml($orderDetail);
        $resXml = self::sendPostReq(self::$urlUnifiedOrder, 'string', $reqXml);
        $resData = Tool::xmlToArray($resXml);
        if($resData['return_code'] == 'FAIL'){
            $resArr['code'] = ErrorCode::WX_PARAM_ERROR;
            $resArr['message'] = $resData['return_msg'];
        } else if($resData['result_code'] == 'FAIL'){
            $resArr['code'] = ErrorCode::WX_PARAM_ERROR;
            $resArr['message'] = $resData['err_code_des'];
        } else {
            //获取支付参数
            $payConfig = new JsPayConfig($orderDetail['appid']);
            $payConfig->setTimeStamp(Tool::getNowTime() . '');
            $payConfig->setPackage($resData['prepay_id']);
            $resArr['data'] = [
                'pay' => $payConfig->getDetail(),
            ];
            unset($payConfig);

            if(in_array($platType, [self::TYPE_SHOP, self::TYPE_OPEN_SHOP,])){
                //获取js参数
                $jsConfig = new JsConfig($orderDetail['appid']);
                $resArr['data']['config'] = $jsConfig->getDetail($platType);
                unset($jsConfig);
            }
        }

        return $resArr;
    }

    /**
     * 发起扫码支付
     * @param \Wx\Shop\UnifiedOrder $order 订单信息
     * @return array
     */
    public static function applyNativePay(UnifiedOrder $order) : array {
        $resArr = [
            'code' => 0,
        ];

        //发起统一下单
        $orderDetail = $order->getDetail();
        $reqXml = Tool::arrayToXml($orderDetail);
        $resXml = self::sendPostReq(self::$urlUnifiedOrder, 'string', $reqXml);
        $resData = Tool::xmlToArray($resXml);
        if($resData['return_code'] == 'FAIL'){
            $resArr['code'] = ErrorCode::WX_PARAM_ERROR;
            $resArr['message'] = $resData['return_msg'];
        } else if($resData['result_code'] == 'FAIL'){
            $resArr['code'] = ErrorCode::WX_PARAM_ERROR;
            $resArr['message'] = $resData['err_code_des'];
        } else {
            $resArr['data'] = [
                'code_url' => self::$urlQrCode . urlencode($resData['code_url']),
                'prepay_id' => $resData['prepay_id'],
            ];
        }

        return $resArr;
    }

    /**
     * 校验数据签名合法性
     * @param array $data 待校验数据
     * @param string $appId
     * @return bool
     */
    public static function checkSign(array $data,string $appId) : bool {
        if (isset($data['sign']) && is_string($data['sign'])) {
            $sign = $data['sign'];
            $nowSign = self::createSign($data, $appId);
            if ($sign === $nowSign) {
                return true;
            }
        }

        return false;
    }

    /**
     * 获取用户授权地址
     * @param string $redirectUrl 跳转地址
     * @param string $type 授权类型 base：静默授权 user：手动授权
     * @param string $appId
     * @return string
     */
    public static function getAuthorizeUrl(string $redirectUrl,string $type,string $appId) : string {
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='
            . WxConfigSingleton::getInstance()->getShopConfig($appId)->getAppId()
            . '&redirect_uri='
            . urlencode($redirectUrl)
            . '&response_type=code&scope=';
        if ($type == 'base') {
            $url .= 'snsapi_base';
        } else {
            $url .= 'snsapi_userinfo';
        }
        $url .= '&state=STATE#wechat_redirect';

        return $url;
    }

    /**
     * 下载微信媒体图片文件
     * @param string $mediaId 媒体ID
     * @param string $path 下载目录,带最后的/
     * @param string $appId
     * @return array
     */
    public static function downloadMediaImage(string $mediaId,string $path,string $appId) : array {
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlDownloadMedia . self::getAccessToken($appId) . '&media_id=' . $mediaId;
        $getRes = self::sendGetReq($url, [
            CURLOPT_TIMEOUT_MS => 3000,
        ]);
        $getData = Tool::jsonDecode($getRes);
        if(is_array($getData) && isset($getData['errcode']) && ($getData['errcode'] == 40001)){ //解决微信缓存刷新导致access token失效的问题
            $url = self::$urlDownloadMedia . self::getAccessToken($appId) . '&media_id=' . $mediaId;
            $getRes = self::sendGetReq($url);
            $getData = Tool::jsonDecode($getRes);
        }

        if(is_array($getData)){
            $resArr['code'] = ErrorCode::WX_GET_ERROR;
            $resArr['message'] = $getData['errmsg'];
        } else {
            $fileName = substr($path, -1) == '/' ? $path : $path . '/';
            $fileName .= $mediaId . '.jpg';
            file_put_contents($fileName, $getRes);
            $resArr['data'] = [
                'image_path' => $fileName,
            ];
        }

        return $resArr;
    }

    /**
     * 获取菜单
     * @param string $appId
     * @return array
     */
    public static function getMenu(string $appId) : array {
        $resArr = [
            'code' => 0
        ];

        $url = self::$urlGetMenu . self::getAccessToken($appId);
        $getRes = self::sendGetReq($url);
        $resData = Tool::jsonDecode($getRes);
        if (isset($resData['menu'])) {
            $resArr['data'] = $resData;
        } else {
            $resArr['code'] = ErrorCode::WX_GET_ERROR;
            $resArr['message'] = '获取菜单列表失败';
        }

        return $resArr;
    }

    /**
     * 删除菜单
     * @param string $appId
     * @return array
     */
    public static function deleteMenu(string $appId) : array {
        $resArr = [
            'code' => 0
        ];

        $url = self::$urlDeleteMenu . self::getAccessToken($appId);
        $getRes = self::sendGetReq($url);
        $resData = Tool::jsonDecode($getRes);
        if ($resData['errcode'] == 0) {
            $resArr['data'] = $resData;
        } else {
            $resArr['code'] = ErrorCode::WX_GET_ERROR;
            $resArr['message'] = $resData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 创建菜单
     * @param array $menuList 菜单列表
     * @param string $appId
     * @return array
     */
    public static function createMenu(array $menuList,string $appId) : array {
        $resArr = [
            'code' => 0
        ];

        $saveArr = [
            'button' => [],
        ];
        foreach ($menuList as $eMenu) {
            if ($eMenu instanceof Menu) {
                $saveArr['button'][] = $eMenu->getDetail();
            }

            if (count($saveArr['button']) == 3) {
                break;
            }
        }
        if (empty($saveArr['button'])) {
            $resArr['code'] = ErrorCode::WX_PARAM_ERROR;
            $resArr['message'] = '菜单列表不能为空';
            return $resArr;
        }

        $url = self::$urlCreateMenu . self::getAccessToken($appId);
        $sendRes = self::sendPostReq($url, 'json', $saveArr);
        $resData = Tool::jsonDecode($sendRes);
        if ($resData['errcode'] == 0) {
            $resArr['data'] = $resData;
        } else {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 获取前端js分享配置
     * @param string $appId
     * @param string $url 链接地址
     * @param string $timestamp 时间戳
     * @param string $nonceStr 随机字符串
     * @return array 空数组为获取失败
     */
    public static function getJsShareConfig(string $appId,string $url,string $timestamp='',string $nonceStr=''){
        $ticket = self::getJsTicket($appId);
        if(strlen($ticket) > 0){
            $nowTime = preg_match('/^[1-4][0-9]{9}$/', $timestamp) > 0 ? $timestamp : Tool::getNowTime() . '';
            $nonce = strlen($nonceStr) >= 16 ? $nonceStr : Tool::createNonceStr(32, 'numlower');
            $needStr = 'jsapi_ticket=' . $ticket . '&noncestr=' . $nonce . '&timestamp=' . $nowTime . '&url=' . $url;
            return [
                'appId' => WxConfigSingleton::getInstance()->getShopConfig($appId)->getAppId(),
                'nonceStr' => $nonce,
                'timestamp' => $nowTime,
                'signature' => sha1($needStr),
            ];
        } else {
            return [];
        }
    }
}