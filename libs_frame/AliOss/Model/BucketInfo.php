<?php
namespace AliOss\Model;

class BucketInfo {
    /**
     * bucket region
     * @var string
     */
    private $location;
    /**
     * bucket name
     * @var string
     */
    private $name;
    /**
     * bucket creation time
     * @var string
     */
    private $createDate;

    /**
     * BucketInfo constructor.
     * @param string $location
     * @param string $name
     * @param string $createDate
     */
    public function __construct($location, $name, $createDate){
        $this->location = $location;
        $this->name = $name;
        $this->createDate = $createDate;
    }

    /**
     * Get bucket location
     * @return string
     */
    public function getLocation(){
        return $this->location;
    }

    /**
     * Get bucket name
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    /**
     * Get bucket creation time.
     * @return string
     */
    public function getCreateDate(){
        return $this->createDate;
    }
}