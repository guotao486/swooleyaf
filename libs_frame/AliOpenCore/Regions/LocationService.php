<?php
namespace AliOpenCore\Regions;

use AliOpenCore\Http\HttpHelper;

class LocationService {
    /**
     * @var \AliOpenCore\Profile\IClientProfile
     */
    private $clientProfile;
    /**
     * @var array
     */
    public static $cache = [];
    /**
     * @var array
     */
    public static $lastClearTimePerProduct = [];
    /**
     * @var string
     */
    public static $serviceDomain = LOCATION_SERVICE_DOMAIN;

    /**
     * AliOpenCore\Regions\LocationService constructor.
     * @param $clientProfile
     */
    public function __construct($clientProfile){
        $this->clientProfile = $clientProfile;
    }

    /**
     * @param $regionId
     * @param $serviceCode
     * @param $endPointType
     * @param $product
     * @return mixed|null
     * @throws \AliOpenCore\Exception\ClientException
     */
    public function findProductDomain($regionId, $serviceCode, $endPointType, $product){
        $key = $regionId . '#' . $product;
        $domain = isset(self::$cache[$key]) ? self::$cache[$key] : null;
        if ($domain === null || $this->checkCacheIsExpire($key) == true) {
            $domain = $this->findProductDomainFromLocationService($regionId, $serviceCode, $endPointType);
            self::$cache[$key] = $domain;
        }

        return $domain;
    }

    /**
     * @param $regionId
     * @param $product
     * @param $domain
     */
    public static function addEndPoint($regionId, $product, $domain){
        $key = $regionId . '#' . $product;
        self::$cache[$key] = $domain;
        $lastClearTime = mktime(0, 0, 0, 1, 1, 2999);
        self::$lastClearTimePerProduct[$key] = $lastClearTime;
    }

    /**
     * @param $domain
     */
    public static function modifyServiceDomain($domain){
        self::$serviceDomain = $domain;
    }

    /**
     * @param $key
     * @return bool
     */
    private function checkCacheIsExpire($key){
        $lastClearTime = isset(self::$lastClearTimePerProduct[$key]) ? self::$lastClearTimePerProduct[$key] : null;
        if ($lastClearTime === null) {
            $lastClearTime = time();
            self::$lastClearTimePerProduct[$key] = $lastClearTime;
        }

        $now = time();
        $elapsedTime = $now - $lastClearTime;

        if ($elapsedTime > CACHE_EXPIRE_TIME) {
            $lastClearTime = time();
            self::$lastClearTimePerProduct[$key] = $lastClearTime;

            return true;
        }

        return false;
    }

    /**
     * @param $regionId
     * @param $serviceCode
     * @param $endPointType
     * @return string|null
     * @throws \AliOpenCore\Exception\ClientException
     */
    private function findProductDomainFromLocationService($regionId, $serviceCode, $endPointType){
        $request = new DescribeEndpointRequest($regionId, $serviceCode, $endPointType);

        $signer = $this->clientProfile->getSigner();
        $credential = $this->clientProfile->getCredential();

        $requestUrl = $request->composeUrl($signer, $credential, self::$serviceDomain);

        $httpResponse = HttpHelper::curl($requestUrl, $request->getMethod(), null, $request->getHeaders());

        if (!$httpResponse->isSuccess()) {
            return null;
        }

        $respObj = json_decode($httpResponse->getBody());

        return $respObj->Endpoints->Endpoint[0]->Endpoint;
    }
}