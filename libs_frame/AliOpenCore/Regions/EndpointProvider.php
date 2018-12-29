<?php
namespace AliOpenCore\Regions;

class EndpointProvider {
    /**
     * @var array
     */
    private static $endpoints;

    /**
     * @param $regionId
     * @param $product
     * @return null
     */
    public static function findProductDomain($regionId, $product){
        if (null == $regionId || null == $product || null == self::$endpoints) {
            return null;
        }
        foreach (self::$endpoints as $key => $endpoint) {
            if (in_array($regionId, $endpoint->getRegionIds())) {
                return self::findProductDomainByProduct($endpoint->getProductDomains(), $product);
            }
        }

        return null;
    }

    /**
     * @param $productDomains
     * @param $product
     * @return null
     */
    private static function findProductDomainByProduct($productDomains, $product){
        if (null == $productDomains) {
            return null;
        }
        foreach ($productDomains as $key => $productDomain) {
            if ($product == $productDomain->getProductName()) {
                return $productDomain->getDomainName();
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public static function getEndpoints(){
        return self::$endpoints;
    }

    /**
     * @param $endpoints
     */
    public static function setEndpoints($endpoints){
        self::$endpoints = $endpoints;
    }
}
