<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/1 0001
 * Time: 9:24
 */
namespace Wx\Mini;

use Constant\ErrorCode;
use Exception\Wx\WxException;

class MsgTemplateAdd extends MiniBase {
    public function __construct(){
        parent::__construct();
    }

    public function __clone(){
    }

    /**
     * 标题ID
     * @var string
     */
    private $titleId = '';
    /**
     * 关键词ID列表
     * @var array
     */
    private $keywordIds = [];

    /**
     * @return string
     */
    public function getTitleId() : string {
        return $this->titleId;
    }

    /**
     * @param string $titleId
     */
    public function setTitleId(string $titleId){
        $this->titleId = trim($titleId);
    }

    /**
     * @return array
     */
    public function getKeywordIds() : array {
        return $this->keywordIds;
    }

    /**
     * @param array $keywordIds
     */
    public function setKeywordIds(array $keywordIds){
        foreach ($keywordIds as $keywordId) {
            if(is_numeric($keywordId) && ($keywordId >= 1)){
                $this->keywordIds[] = (int)$keywordId;
            }
        }
    }

    /**
     * @param int $keywordId
     */
    public function addKeywordId(int $keywordId){
        $this->keywordIds[] = $keywordId;
    }

    public function getDetail() : array{
        if(strlen($this->titleId) == 0){
            throw new WxException('标题ID不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        array_unique($this->keywordIds);
        $keywordNum = count($this->keywordIds);
        if($keywordNum == 0){
            throw new WxException('关键词不能为空', ErrorCode::WX_PARAM_ERROR);
        } else if($keywordNum > 10){
            throw new WxException('关键词不能超过10个', ErrorCode::WX_PARAM_ERROR);
        }

        return [
            'id' => $this->titleId,
            'keyword_id_list' => $this->keywordIds,
        ];
    }
}