<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/8/17 0017
 * Time: 11:40
 */
namespace Map\BaiDu;

use Constant\ErrorCode;
use Exception\Map\BaiduMapException;
use Map\MapSimpleTrait;

class GeoCoder extends MapBase {
    use MapSimpleTrait;

    public function __construct(){
        parent::__construct();
        $this->coordTypeReturn = 'bd09ll';
    }

    /**
     * 地址
     * @var string
     */
    private $address = '';
    /**
     * 城市名
     * @var string
     */
    private $cityName = '';
    /**
     * 返回的坐标类型
     * @var string
     */
    private $coordTypeReturn = '';

    /**
     * @return string
     */
    public function getAddress() : string {
        return $this->address;
    }

    /**
     * @param string $address
     * @throws \Exception\Map\BaiduMapException
     */
    public function setAddress(string $address){
        if(strlen($address) > 0){
            $this->address = mb_substr($address, 0, 42);
        } else {
            throw new BaiduMapException('地址不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getCityName() : string {
        return $this->cityName;
    }

    /**
     * @param string $cityName
     */
    public function setCityName(string $cityName){
        $this->cityName = $cityName;
    }

    /**
     * @return string
     */
    public function getCoordTypeReturn() : string {
        return $this->coordTypeReturn;
    }

    /**
     * @param string $coordTypeReturn
     */
    public function setCoordTypeReturn(string $coordTypeReturn){
        $this->coordTypeReturn = $coordTypeReturn;
    }
}