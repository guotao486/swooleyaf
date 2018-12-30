<?php
namespace AliOpenCore\Auth;

use AliOpenCore\RpcAcsRequest;

class AssumeRoleRequest extends RpcAcsRequest {
    /**
     * AliOpenCore\Auth\AssumeRoleRequest constructor.
     * @param $roleArn
     * @param $roleSessionName
     */
    public function __construct($roleArn, $roleSessionName){
        parent::__construct(STS_PRODUCT_NAME, STS_VERSION, STS_ACTION);

        $this->queryParameters['RoleArn'] = $roleArn;
        $this->queryParameters['RoleSessionName'] = $roleSessionName;
        $this->queryParameters['DurationSeconds'] = ROLE_ARN_EXPIRE_TIME;
        $this->setRegionId(ROLE_ARN_EXPIRE_TIME);
        $this->setProtocol('https');

        $this->setAcceptFormat('JSON');
    }
}
