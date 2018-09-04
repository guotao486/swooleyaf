<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/8/17 0017
 * Time: 11:41
 */
namespace Map\BaiDu;

use Constant\ErrorCode;
use Exception\Map\BaiduMapException;
use Map\MapSimpleTrait;

class GeoCoderReverse extends MapBase {
    use MapSimpleTrait;

    public function __construct(){
        parent::__construct();
        $this->coordType = 'bd09ll';
        $this->coordTypeReturn = 'bd09ll';
        $this->poiStatus = 0;
        $this->poiRadius = 1000;
    }

    /**
     * 坐标地址
     * @var string
     */
    private $location = '';
    /**
     * 坐标类型
     * @var string
     */
    private $coordType = '';
    /**
     * 返回的坐标类型
     * @var string
     */
    private $coordTypeReturn = '';
    /**
     * poi召回状态,0为不召回,1为召回
     * @var int
     */
    private $poiStatus = 0;
    /**
     * poi召回半径,单位为米
     * @var int
     */
    private $poiRadius = 0;

    /**
     * @return string
     */
    public function getLocation() : string {
        return $this->location;
    }

    /**
     * @param double $lat 纬度
     * @param double $lng 经度
     */
    public function setLocation($lat, $lng){
        $this->location = $lat . ',' . $lng;
    }

    /**
     * @return string
     */
    public function getCoordType() : string {
        return $this->coordType;
    }

    /**
     * @param string $coordType
     */
    public function setCoordType(string $coordType){
        $this->coordType = $coordType;
        $this->coordTypeReturn = $coordType;
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

    /**
     * @return int
     */
    public function getPoiStatus() : int {
        return $this->poiStatus;
    }

    /**
     * @param int $poiStatus
     * @throws \Exception\Map\BaiduMapException
     */
    public function setPoiStatus(int $poiStatus){
        if(in_array($poiStatus, [0, 1])){
            $this->poiStatus = $poiStatus;
        } else {
            throw new BaiduMapException('poi召回状态不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return int
     */
    public function getPoiRadius() : int {
        return $this->poiRadius;
    }

    /**
     * @param int $poiRadius
     * @throws \Exception\Map\BaiduMapException
     */
    public function setPoiRadius(int $poiRadius){
        if(($poiRadius >= 0) && ($poiRadius <= 1000)){
            $this->poiRadius = $poiRadius;
        } else {
            throw new BaiduMapException('poi召回半径不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }
}