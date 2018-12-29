<?php
namespace AliOpenCore\Regions;

use AliOpenCore\RpcAcsRequest;

class DescribeEndpointRequest extends RpcAcsRequest {
    /**
     * AliOpenCore\Regions\DescribeEndpointRequest constructor.
     * @param $id
     * @param $serviceCode
     * @param $endPointType
     */
    public function __construct($id, $serviceCode, $endPointType){
        parent::__construct(LOCATION_SERVICE_PRODUCT_NAME, LOCATION_SERVICE_VERSION, LOCATION_SERVICE_DESCRIBE_ENDPOINT_ACTION);

        $this->queryParameters['Id'] = $id;
        $this->queryParameters['ServiceCode'] = $serviceCode;
        $this->queryParameters['Type'] = $endPointType;
        $this->setRegionId(LOCATION_SERVICE_REGION);

        $this->setAcceptFormat('JSON');
    }
}