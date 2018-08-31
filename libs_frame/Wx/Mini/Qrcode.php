<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/1/26 0026
 * Time: 18:07
 */
namespace Wx\Mini;

use Constant\ErrorCode;
use Exception\Wx\WxException;

class Qrcode {
    public function __construct(){
    }

    private function __clone(){
    }

    /**
     * 场景
     * @var string
     */
    private $scene = '';
    /**
     * 页面地址
     * @var string
     */
    private $page = '';
    /**
     * 二维码宽度
     * @var int
     */
    private $width = 430;
    /**
     * 线条颜色配置
     * @var bool
     */
    private $auto_color = false;
    /**
     * 线条rgb颜色
     * @var array
     */
    private $line_color = [
        'r' => '0',
        'g' => '0',
        'b' => '0',
    ];

    /**
     * @param string $scene
     * @throws \Exception\Wx\WxException
     */
    public function setScene(string $scene){
        $trueScene = trim($scene);
        $length = strlen($trueScene);
        if($length == 0){
            throw new WxException('场景标识不能为空', ErrorCode::WX_PARAM_ERROR);
        } else if($length > 32){
            throw new WxException('场景标识不能超过32个字符', ErrorCode::WX_PARAM_ERROR);
        }

        $this->scene = $trueScene;
    }

    /**
     * @param string $page
     * @throws \Exception\Wx\WxException
     */
    public function setPage(string $page){
        $truePage = trim($page);
        if(strlen($truePage) > 0){
            $this->page = $truePage;
        } else {
            throw new WxException('页面地址不能为空', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param int $width
     * @throws \Exception\Wx\WxException
     */
    public function setWidth(int $width){
        if ($width > 0) {
            $this->width = $width;
        } else {
            throw new WxException('二维码宽度必须大于0', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param bool $autoColor
     */
    public function setAutoColor(bool $autoColor){
        $this->auto_color = $autoColor;
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     * @throws \Exception\Wx\WxException
     */
    public function setLineColor(int $red,int $green,int $blue){
        if(($red < 0) || ($red > 255)){
            throw new WxException('线条颜色red不合法', ErrorCode::WX_PARAM_ERROR);
        } else if(($green < 0) || ($green > 255)){
            throw new WxException('线条颜色green不合法', ErrorCode::WX_PARAM_ERROR);
        } else if(($blue < 0) || ($blue > 255)){
            throw new WxException('线条颜色blue不合法', ErrorCode::WX_PARAM_ERROR);
        }

        $this->line_color = [
            'r' => (string)$red,
            'g' => (string)$green,
            'b' => (string)$blue,
        ];
    }

    public function getDetail() : array {
        if(strlen($this->scene) == 0){
            throw new WxException('场景标识必须填写', ErrorCode::WX_PARAM_ERROR);
        } else if(strlen($this->page) == 0){
            throw new WxException('页面地址必须填写', ErrorCode::WX_PARAM_ERROR);
        }

        return [
            'scene' => $this->scene,
            'page' => $this->page,
            'width' => $this->width,
            'auto_color' => $this->auto_color,
            'line_color' => $this->line_color,
        ];
    }
}