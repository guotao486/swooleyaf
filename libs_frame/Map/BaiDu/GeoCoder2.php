<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 18-9-9
 * Time: 下午3:14
 */
namespace Map\BaiDu;

use Map\MapBaseBaiDu;

class GeoCoder2 extends MapBaseBaiDu {
    public function __construct(){
        parent::__construct();
    }

    public function __clone(){
    }

    public function getDetail() : array {
        $content = $this->getContent();
        $detailArr = $this->curlConfigs;
        $detailArr[CURLOPT_URL] = $this->getServiceUrl() . '?' . http_build_query($content);
        return $detailArr;
    }
}