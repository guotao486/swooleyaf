<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/12 0012
 * Time: 15:59
 */
namespace Wx2\Shop;

use Constant\ErrorCode;
use Exception\Wx\WxException;
use Tool\Tool;
use Wx2\WxBaseShop;
use Wx2\WxUtilBase;
use Wx2\WxUtilBaseAlone;

class MenuCreate extends WxBaseShop {
    /**
     * 菜单列表
     * @var array
     */
    private $menuList = [];

    public function __construct(string $appId){
        parent::__construct();
        $this->serviceUrl = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=';
        $this->reqData['appid'] = $appId;
    }

    public function __clone(){
    }

    /**
     * @param \Wx2\Shop\Menu $menu
     * @throws \Exception\Wx\WxException
     */
    public function addMenu(Menu $menu){
        if(count($this->menuList) >= 3){
            throw new WxException('菜单数量不能超过3个', ErrorCode::WX_PARAM_ERROR);
        }

        $this->menuList[] = $menu;
    }

    public function getDetail() : array {
        if(empty($this->menuList)){
            throw new WxException('菜单列表不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr = [
            'code' => 0
        ];

        $this->reqData['menu_list'] = [
            'button' => [],
        ];
        foreach ($this->menuList as $eMenu) {
            $this->reqData['menu_list']['button'][] = $eMenu->getDetail();
        }

        $this->curlConfigs[CURLOPT_URL] = $this->serviceUrl . WxUtilBaseAlone::getAccessToken($this->reqData['appid']);
        $this->curlConfigs[CURLOPT_POSTFIELDS] = Tool::jsonEncode($this->reqData['menu_list'], JSON_UNESCAPED_UNICODE);
        $sendRes = WxUtilBase::sendPostReq($this->curlConfigs);
        $sendData = Tool::jsonDecode($sendRes);
        if ($sendData['errcode'] == 0) {
            $resArr['data'] = $sendData;
        } else {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $sendData['errmsg'];
        }

        return $resArr;
    }
}