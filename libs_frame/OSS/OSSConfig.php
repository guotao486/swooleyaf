<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/7/19 0019
 * Time: 12:13
 */
namespace OSS;

use Constant\ErrorCode;
use Exception\OSS\OSSException;

class OSSConfig {
    /**
     * 内网URL
     * @var string
     */
    private $addressInner = '';
    /**
     * 外网URL
     * @var string
     */
    private $addressOuter = '';
    /**
     * 帐号ID
     * @var string
     */
    private $keyId = '';
    /**
     * 帐号密钥
     * @var string
     */
    private $keySecret = '';
    /**
     * 桶名称
     * @var string
     */
    private $bucketName = '';
    /**
     * 桶域名
     * @var string
     */
    private $bucketDomain = '';

    public function __construct(){
    }

    private function __clone(){
    }

    /**
     * @return string
     */
    public function getAddressInner() : string {
        return $this->addressInner;
    }

    /**
     * @param string $addressInner
     * @throws \Exception\OSS\OSSException
     */
    public function setAddressInner(string $addressInner){
        if(preg_match('/^(http|https)\:\/\/\S+$/', $addressInner) > 0){
            $this->addressInner = $addressInner;
        } else {
            throw new OSSException('内网URL不合法', ErrorCode::OSS_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getAddressOuter() : string {
        return $this->addressOuter;
    }

    /**
     * @param string $addressOuter
     * @throws \Exception\OSS\OSSException
     */
    public function setAddressOuter(string $addressOuter){
        if(preg_match('/^(http|https)\:\/\/\S+$/', $addressOuter) > 0){
            $this->addressOuter = $addressOuter;
        } else {
            throw new OSSException('外网URL不合法', ErrorCode::OSS_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getKeyId() : string {
        return $this->keyId;
    }

    /**
     * @param string $keyId
     * @throws \Exception\OSS\OSSException
     */
    public function setKeyId(string $keyId){
        if(ctype_alnum($keyId)){
            $this->keyId = $keyId;
        } else {
            throw new OSSException('帐号ID不合法', ErrorCode::OSS_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getKeySecret() : string {
        return $this->keySecret;
    }

    /**
     * @param string $keySecret
     * @throws \Exception\OSS\OSSException
     */
    public function setKeySecret(string $keySecret){
        if(ctype_alnum($keySecret)){
            $this->keySecret = $keySecret;
        } else {
            throw new OSSException('帐号密钥不合法', ErrorCode::OSS_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getBucketName() : string {
        return $this->bucketName;
    }

    /**
     * @param string $bucketName
     * @throws \Exception\OSS\OSSException
     */
    public function setBucketName(string $bucketName){
        if (strlen($bucketName) > 0) {
            $this->bucketName = $bucketName;
        } else {
            throw new OSSException('桶名称不合法', ErrorCode::OSS_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getBucketDomain() : string {
        return $this->bucketDomain;
    }

    /**
     * @param string $bucketDomain
     * @throws \Exception\OSS\OSSException
     */
    public function setBucketDomain(string $bucketDomain){
        if(preg_match('/^(http|https)\:\/\/\S+$/', $bucketDomain) > 0){
            $this->bucketDomain = $bucketDomain;
        } else {
            throw new OSSException('桶域名不合法', ErrorCode::OSS_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        return [
            'address.inner' => $this->addressInner,
            'address.outer' => $this->addressOuter,
            'key.id' => $this->keyId,
            'key.secret' => $this->keySecret,
            'bucket.name' => $this->bucketName,
            'bucket.domain' => $this->bucketDomain,
        ];
    }
}