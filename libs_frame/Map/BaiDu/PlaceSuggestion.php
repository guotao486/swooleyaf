<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/8/18 0018
 * Time: 9:02
 */
namespace Map\BaiDu;

use Constant\ErrorCode;
use Exception\Map\BaiduMapException;
use Map\MapSimpleTrait;

class PlaceSuggestion extends MapBase {
    use MapSimpleTrait;

    public function __construct(){
        parent::__construct();
        $this->cityLimit = 'true';
        $this->coordType = 3;
        $this->coordTypeReturn = 'bd09ll';
    }

    /**
     * 关键词
     * @var string
     */
    private $keyword = '';
    /**
     * 区域
     * @var string
     */
    private $region = '';
    /**
     * 区域限制标识,true:只返回region中指定城市检索结果
     * @var string
     */
    private $cityLimit = '';
    /**
     * 地址
     * @var string
     */
    private $location = '';
    /**
     * 坐标类型
     * @var int
     */
    private $coordType = 0;
    /**
     * 返回的坐标类型
     * @var string
     */
    private $coordTypeReturn = '';

    /**
     * @return string
     */
    public function getKeyword() : string {
        return $this->keyword;
    }

    /**
     * @param string $keyword
     * @throws \Exception\Map\BaiduMapException
     */
    public function setKeyword(string $keyword){
        if(strlen($keyword) > 0){
            $this->keyword = $keyword;
        } else {
            throw new BaiduMapException('关键词不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
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
     * @throws \Exception\Map\BaiduMapException
     */
    public function setRegion(string $region){
        if(strlen($region) > 0){
            $this->region = $region;
        } else {
            throw new BaiduMapException('区域不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getCityLimit() : string {
        return $this->cityLimit;
    }

    /**
     * @param string $cityLimit
     * @throws \Exception\Map\BaiduMapException
     */
    public function setCityLimit(string $cityLimit){
        if(in_array($cityLimit, ['true', 'false'])){
            $this->cityLimit = $cityLimit;
        } else {
            throw new BaiduMapException('区域限制不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

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
    public function getCoordType() : int {
        return $this->coordType;
    }

    /**
     * @param int $coordType
     * @throws \Exception\Map\BaiduMapException
     */
    public function setCoordType(int $coordType){
        if(in_array($coordType, [1, 2, 3, 4,])){
            $this->coordType = $coordType;
        } else {
            throw new BaiduMapException('坐标类型不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getCoordTypeReturn() : string {
        return $this->coordTypeReturn;
    }

    /**
     * @param string $coordTypeReturn
     * @throws \Exception\Map\BaiduMapException
     */
    public function setCoordTypeReturn(string $coordTypeReturn){
        if(strlen($coordTypeReturn) > 0){
            $this->coordTypeReturn = $coordTypeReturn;
        } else {
            throw new BaiduMapException('返回坐标类型不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }
}