<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/8/18 0018
 * Time: 14:07
 */
namespace Map\Tencent;

use Constant\ErrorCode;
use Exception\Map\TencentMapException;
use Tool\Tool;

class GeoCoder extends MapBase {
    public function __construct() {
        parent::__construct();
    }

    private function __clone() {
    }

    public function __toString() {
        $vars = array_merge(get_object_vars($this), parent::getConfigs());

        return Tool::jsonEncode($vars, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 地址
     * @var string
     */
    private $address = '';
    /**
     * 地区
     * @var string
     */
    private $region = '';

    /**
     * @return string
     */
    public function getAddress() : string {
        return $this->address;
    }

    /**
     * @param string $address
     * @throws \Exception\Map\TencentMapException
     */
    public function setAddress(string $address){
        if(strlen($address) > 0){
            $this->address = $address;
        } else {
            throw new TencentMapException('地址不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getRegion() : string {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion(string $region){
        $this->region = $region;
    }
}