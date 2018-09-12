<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/12 0012
 * Time: 8:38
 */
namespace Wx2\Shop;

use Constant\ErrorCode;
use Exception\Wx\WxException;
use Tool\Tool;
use Wx2\WxBaseShop;
use Wx2\WxUtilBase;
use Wx2\WxUtilBaseAlone;

class DownloadMedia extends WxBaseShop {
    /**
     * 输出目录
     * @var string
     */
    private $output_dir = '';
    /**
     * 媒体ID
     * @var string
     */
    private $media_id = '';

    public function __construct(string $appId){
        parent::__construct();
        $this->serviceUrl = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=';
        $this->reqData['appid'] = $appId;
    }

    public function __clone(){
    }

    /**
     * @param string $outputDir
     * @throws \Exception\Wx\WxException
     */
    public function setOutputDir(string $outputDir){
        if(is_dir($outputDir) && is_writeable($outputDir)){
            $this->reqData['output_dir'] = substr($outputDir, -1) == '/' ? $outputDir : $outputDir . '/';
        } else {
            throw new WxException('输出目录不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $mediaId
     * @throws \Exception\Wx\WxException
     */
    public function setMediaId(string $mediaId){
        if(strlen($mediaId) > 0){
            $this->reqData['media_id'] = $mediaId;
        } else {
            throw new WxException('媒体ID不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(!isset($this->reqData['output_dir'])){
            throw new WxException('输出目录不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if(!isset($this->reqData['media_id'])){
            throw new WxException('媒体ID不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr = [
            'code' => 0,
        ];

        $this->curlConfigs[CURLOPT_URL] = $this->serviceUrl . WxUtilBaseAlone::getAccessToken($this->reqData['appid']) . '&media_id=' . $this->reqData['media_id'];
        $this->curlConfigs[CURLOPT_TIMEOUT_MS] = 3000;
        $sendRes = WxUtilBase::sendGetReq($this->curlConfigs);
        $sendData = Tool::jsonDecode($sendRes);
        if(is_array($sendData)){
            $resArr['code'] = ErrorCode::WX_GET_ERROR;
            $resArr['message'] = $sendData['errmsg'];
        } else {
            $fileName = $this->reqData['output_dir'] . $this->reqData['media_id'] . '.jpg';
            file_put_contents($fileName, $sendRes);
            $resArr['data'] = [
                'image_path' => $fileName,
            ];
        }

        return $resArr;
    }
}