<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-04
 * Time: 18:14
 */
namespace Wx\Shop;

use Constant\ErrorCode;
use Exception\Wx\WxException;

class Menu extends ShopBase {
    private static $typeList = [
        'pic_weixin',
        'pic_sysphoto',
        'pic_photo_or_album',
        'view',
        'view_limited',
        'click',
        'media_id',
        'location_select',
        'scancode_push',
        'scancode_waitmsg',
    ];

    public function __construct() {
        parent::__construct();
    }

    private function __clone(){
    }

    /**
     * 菜单标题
     * @var string
     */
    private $name = '';

    /**
     * 子菜单
     * @var array
     */
    private $sub_button = [];

    /**
     * 响应动作类型
     * @var string
     */
    private $type = '';

    /**
     * 菜单KEY值，用于消息接口推送
     * @var string
     */
    private $key = '';

    /**
     * 网页链接，用户点击菜单可打开链接
     * @var string
     */
    private $url = '';

    /**
     * 媒体ID
     * @var string
     */
    private $media_id = '';

    /**
     * @param string $name
     * @throws \Exception\Wx\WxException
     */
    public function setName(string $name) {
        if (strlen($name) > 0) {
            $this->name = mb_substr($name, 0, 5);
        } else {
            throw new WxException('菜单名称不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param array $sub
     * @throws \Exception\Wx\WxException
     */
    public function addSub(array $sub) {
        if (empty($sub)) {
            throw new WxException('子菜单不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        if (count($this->sub_button) < 5) {
            $this->sub_button[] = $sub;
        } else {
            throw  new WxException('子菜单不能超过5个', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $type
     * @throws \Exception\Wx\WxException
     */
    public function setType(string $type) {
        if (in_array($type, self::$typeList)) {
            $this->type = $type;
        } else {
            throw  new WxException('响应动作类型不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $key
     */
    public function setKey(string $key) {
        $this->key = substr($key, 0, 128);
    }

    /**
     * @param string $url
     * @throws \Exception\Wx\WxException
     */
    public function setUrl(string $url) {
        if (preg_match('/^(http|https)\:\/\/\S+$/', $url) > 0) {
            $this->url = $url;
        } else {
            throw new WxException('网页链接不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $mediaId
     * @throws \Exception\Wx\WxException
     */
    public function setMediaId(string $mediaId) {
        if (strlen($mediaId) > 0) {
            $this->media_id = $mediaId;
        } else {
            throw new WxException('媒体ID不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(strlen($this->name) == 0){
            throw new WxException('菜单名称不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if(strlen($this->type) == 0){
            throw new WxException('响应动作类型不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr = [
            'name' => $this->name,
            'type' => $this->type,
            'sub_button' => $this->sub_button,
        ];
        if(strlen($this->key) > 0){
            $resArr['key'] = $this->key;
        }
        if(strlen($this->url) > 0){
            $resArr['url'] = $this->url;
        }
        if(strlen($this->media_id) > 0){
            $resArr['media_id'] = $this->media_id;
        }

        return $resArr;
    }
}