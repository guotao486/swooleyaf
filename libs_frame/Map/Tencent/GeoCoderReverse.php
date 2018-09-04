<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/8/18 0018
 * Time: 14:08
 */
namespace Map\Tencent;

use Constant\ErrorCode;
use Exception\Map\TencentMapException;
use Map\SimpleTraitMap;

class GeoCoderReverse extends MapBase {
    use SimpleTraitMap;

    public function __construct() {
        parent::__construct();
        $this->poiStatus = 0;
    }

    /**
     * 坐标
     * @var string
     */
    private $location = '';
    /**
     * poi状态 0:不返回 1:返回
     * @var int
     */
    private $poiStatus = 0;
    /**
     * poi选项列表
     * @var array
     */
    private $poiOptions = [];

    /**
     * @return string
     */
    public function getLocation() : string {
        return $this->location;
    }

    /**
     * @param double $lat
     * @param double $lng
     */
    public function setLocation($lat, $lng){
        $this->location = $lat . ',' . $lng;
    }

    /**
     * @return int
     */
    public function getPoiStatus() : int {
        return $this->poiStatus;
    }

    /**
     * @param int $poiStatus
     * @throws \Exception\Map\TencentMapException
     */
    public function setPoiStatus(int $poiStatus){
        if(in_array($poiStatus, [0, 1])){
            $this->poiStatus = $poiStatus;
        } else {
            throw new TencentMapException('poi状态不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }

    /**
     * @return array
     */
    public function getPoiOptions() : array {
        return $this->poiOptions;
    }

    /**
     * @param array $poiOptions
     */
    public function addPoiOptions(array $poiOptions){
        foreach ($poiOptions as $poiKey => $poiVal) {
            $this->poiOptions[$poiKey] = $poiVal;
        }
    }

    /**
     * @param array $poiOptions
     */
    public function setPoiOptions(array $poiOptions){
        $this->poiOptions = $poiOptions;
    }
}