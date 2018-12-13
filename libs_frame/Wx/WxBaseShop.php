<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/11 0011
 * Time: 8:52
 */
namespace Wx;

abstract class WxBaseShop extends WxBase {
    const MATERIAL_TYPE_IMAGE = 'image';
    const MATERIAL_TYPE_VOICE = 'voice';
    const MATERIAL_TYPE_VIDEO = 'video';
    const MATERIAL_TYPE_THUMB = 'thumb';

    protected static $totalMaterialType = [
        self::MATERIAL_TYPE_IMAGE => '图片',
        self::MATERIAL_TYPE_VOICE => '语音',
        self::MATERIAL_TYPE_VIDEO => '视频',
        self::MATERIAL_TYPE_THUMB => '缩略图',
    ];

    public function __construct(){
        parent::__construct();
    }
}