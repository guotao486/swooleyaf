<?php
/**
 * OSS单例类
 * User: 姜伟
 * Date: 2017/1/6 0006
 * Time: 9:38
 */
namespace DesignPatterns\Singletons;

use Constant\ErrorCode;
use Exception\OSS\OSSException;
use Log\Log;
use OSS\OssClient;
use OSS\OSSConfig;
use Tool\Tool;
use Traits\SingletonTrait;

class OSSSingleton {
    use SingletonTrait;
    /**
     * @var \OSS\OSSConfig
     */
    private $ossConfig = null;
    /**
     * 内网访问客户端
     * @var \OSS\OssClient
     */
    private $innerClient = null;
    /**
     * 外网访问客户端
     * @var \OSS\OssClient
     */
    private $outerClient = null;

    private function __construct() {
        $configs = Tool::getConfig('oss.' . SY_ENV . SY_PROJECT);
        $ossConfig = new OSSConfig();

        try {
            $ossConfig->setKeyId((string)Tool::getArrayVal($configs, 'access.key.id', '', true));
            $ossConfig->setKeySecret((string)Tool::getArrayVal($configs, 'access.key.secret', '', true));
            $ossConfig->setAddressInner((string)Tool::getArrayVal($configs, 'server.address.inner', '', true));
            $ossConfig->setAddressOuter((string)Tool::getArrayVal($configs, 'server.address.outer', '', true));
            $ossConfig->setBucketName((string)Tool::getArrayVal($configs, 'bucket.name', '', true));
            $ossConfig->setBucketDomain((string)Tool::getArrayVal($configs, 'bucket.domain', '', true));
            $this->ossConfig = $ossConfig;

            $this->innerClient = new OssClient($this->ossConfig->getKeyId(), $this->ossConfig->getKeySecret(), $this->ossConfig->getAddressInner());
            $this->outerClient = new OssClient($this->ossConfig->getKeyId(), $this->ossConfig->getKeySecret(), $this->ossConfig->getAddressOuter());
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getCode(), $e->getTraceAsString());

            $this->ossConfig = null;
            $this->innerClient = null;
            $this->outerClient = null;

            throw new OSSException('OSS连接出错', ErrorCode::OSS_CONNECT_ERROR);
        }
    }

    /**
     * @return \DesignPatterns\Singletons\OSSSingleton
     */
    public static function getInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return null|\OSS\OSSConfig
     */
    public function getConfig(){
        return $this->ossConfig;
    }

    /**
     * @return string
     */
    public function getBucketName() : string {
        return $this->ossConfig->getBucketName();
    }

    /**
     * @return string
     */
    public function getBucketDomain() : string {
        return $this->ossConfig->getBucketDomain();
    }

    /**
     * 上传文件到阿里云
     * @param string $fileName oss上保存的文件名称
     * @param string $file 上传文件路径(包括名称)
     * @return array
     * @throws \Exception\OSS\OSSException
     */
    public function upload($fileName, $file) {
        try {
            $fileInfo = file_get_contents($file);
            $addRes = $this->innerClient->putObject($this->ossConfig->getBucketName(), $fileName, $fileInfo);

            return $addRes;
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getCode(), $e->getTraceAsString());

            throw new OSSException(ErrorCode::getMsg(ErrorCode::OSS_UPLOAD_FILE_ERROR), ErrorCode::OSS_UPLOAD_FILE_ERROR);
        }
    }

    /**
     * 删除上传的文件
     * @param string $fileName 上传文件的文件名称，包括路径和后缀
     * @return mixed
     * @throws \Exception\OSS\OSSException
     */
    public function delFile($fileName) {
        try {
            $delRes = $this->outerClient->deleteObject($this->ossConfig->getBucketName(), $fileName);

            return $delRes;
        } catch(\Exception $e) {
            Log::error($e->getMessage(), $e->getCode(), $e->getTraceAsString());

            throw new OSSException(ErrorCode::getMsg(ErrorCode::OSS_DELETE_FILE_ERROR), ErrorCode::OSS_DELETE_FILE_ERROR);
        }
    }

    /**
     * 获取bucket中所有文件的文件名称
     * @return array
     * @throws \Exception\OSS\OSSException
     */
    public function getAllFileNames() {
        try {
            $objList = $this->outerClient->listObjects($this->ossConfig->getBucketName())->getObjectList();
            $objKeys = [];
            foreach($objList as $eObj) {
                $objKeys[] = $eObj->getKey();
            }

            return $objKeys;
        } catch(\Exception $e) {
            Log::error($e->getMessage(), $e->getCode(), $e->getTraceAsString());

            throw new OSSException(ErrorCode::getMsg(ErrorCode::OSS_GET_BUCKET_FILE_ERROR), ErrorCode::OSS_GET_BUCKET_FILE_ERROR);
        }
    }
}