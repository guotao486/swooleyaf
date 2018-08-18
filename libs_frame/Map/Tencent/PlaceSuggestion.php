<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/8/18 0018
 * Time: 14:06
 */
namespace Map\Tencent;

use Constant\ErrorCode;
use Exception\Map\TencentMapException;
use Tool\Tool;

class PlaceSuggestion extends MapBase {
    public function __construct() {
        parent::__construct();

        $this->regionLimit = 0;
        $this->subLimit = 0;
        $this->policy = 0;
        $this->page = 1;
        $this->limit = 10;
    }

    private function __clone() {
    }

    public function __toString() {
        $vars = array_merge(get_object_vars($this), parent::getConfigs());

        return Tool::jsonEncode($vars, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 关键词
     * @var string
     */
    private $keyword = '';
    /**
     * 地区
     * @var string
     */
    private $region = '';
    /**
     * 地区限制标识,0：当前城市无结果时,自动扩大范围到全国匹配 1：固定在当前城市
     * @var int
     */
    private $regionLimit = 0;
    /**
     * 定位坐标
     * @var string
     */
    private $location = '';
    /**
     * 子地点限制标识 0:不返回 1:返回
     * @var int
     */
    private $subLimit = 0;
    /**
     * 检索策略
     * @var int
     */
    private $policy = 0;
    /**
     * 筛选条件
     * @var array
     */
    private $filters = [];
    /**
     * 页码
     * @var int
     */
    private $page = 0;
    /**
     * 每页条数
     * @var int
     */
    private $limit = 0;

    /**
     * @return string
     */
    public function getKeyword() : string {
        return $this->keyword;
    }

    /**
     * @param string $keyword
     * @throws \Exception\Map\TencentMapException
     */
    public function setKeyword(string $keyword){
        if(strlen($keyword) > 0){
            $this->keyword = $keyword;
        } else {
            throw new TencentMapException('关键词不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
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
     * @throws \Exception\Map\TencentMapException
     */
    public function setRegion(string $region){
        if(strlen($region) > 0){
            $this->region = $region;
        } else {
            throw new TencentMapException('地区不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }

    /**
     * @return int
     */
    public function getRegionLimit() : int {
        return $this->regionLimit;
    }

    /**
     * @param int $regionLimit
     * @throws \Exception\Map\TencentMapException
     */
    public function setRegionLimit(int $regionLimit){
        if(in_array($regionLimit, [0, 1])){
            $this->regionLimit = $regionLimit;
        } else {
            throw new TencentMapException('地区限制不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
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
    public function getSubLimit() : int {
        return $this->subLimit;
    }

    /**
     * @param int $subLimit
     * @throws \Exception\Map\TencentMapException
     */
    public function setSubLimit(int $subLimit){
        if(in_array($subLimit, [0, 1])){
            $this->subLimit = $subLimit;
        } else {
            throw new TencentMapException('子地点限制不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }

    /**
     * @return int
     */
    public function getPolicy() : int {
        return $this->policy;
    }

    /**
     * @param int $policy
     */
    public function setPolicy(int $policy){
        $this->policy = $policy;
    }

    /**
     * @return array
     */
    public function getFilters() : array {
        return $this->filters;
    }

    /**
     * @param array $filters
     */
    public function addFilters(array $filters){
        foreach ($filters as $key => $val) {
            $this->filters[$key] = $val;
        }
    }

    /**
     * @param array $filters
     */
    public function setFilters(array $filters){
        $this->filters = $filters;
    }

    /**
     * @return int
     */
    public function getPage() : int {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page){
        $this->page = $page > 0 ? $page : 1;
    }

    /**
     * @return int
     */
    public function getLimit() : int {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @throws \Exception\Map\TencentMapException
     */
    public function setLimit(int $limit){
        if(($limit > 0) && ($limit <= 20)){
            $this->limit = $limit;
        } else {
            throw new TencentMapException('每页条数不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }
}