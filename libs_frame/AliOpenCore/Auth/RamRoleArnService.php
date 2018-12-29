<?php
namespace AliOpenCore\Auth;

use AliOpenCore\Exception\ClientException;
use AliOpenCore\Http\HttpHelper;
use AliOpenCore\Profile\IClientProfile;

class RamRoleArnService {
    /**
     * @var IClientProfile
     */
    private $clientProfile;
    /**
     * @var null|string
     */
    private $lastClearTime = null;
    /**
     * @var null|string
     */
    private $sessionCredential = null;
    /**
     * @var string
     */
    public static $serviceDomain = STS_DOMAIN;

    /**
     * AliOpenCore\Auth\RamRoleArnService constructor.
     * @param $clientProfile
     */
    public function __construct($clientProfile){
        $this->clientProfile = $clientProfile;
    }

    /**
     * @return Credential|string|null
     * @throws ClientException
     */
    public function getSessionCredential(){
        if ($this->lastClearTime != null && $this->sessionCredential != null) {
            $now = time();
            $elapsedTime = $now - $this->lastClearTime;
            if ($elapsedTime <= ROLE_ARN_EXPIRE_TIME * 0.8) {
                return $this->sessionCredential;
            }
        }

        $credential = $this->assumeRole();

        if ($credential == null) {
            return null;
        }

        $this->sessionCredential = $credential;
        $this->lastClearTime = time();

        return $credential;
    }

    /**
     * @return Credential|null
     * @throws ClientException
     */
    private function assumeRole(){
        $signer = $this->clientProfile->getSigner();
        $ramRoleArnCredential = $this->clientProfile->getCredential();

        $request =
            new \AliOpenCore\Auth\AssumeRoleRequest($ramRoleArnCredential->getRoleArn(), $ramRoleArnCredential->getRoleSessionName());

        $requestUrl = $request->composeUrl($signer, $ramRoleArnCredential, self::$serviceDomain);

        $httpResponse = HttpHelper::curl($requestUrl, $request->getMethod(), null, $request->getHeaders());

        if (!$httpResponse->isSuccess()) {
            return null;
        }

        $respObj = json_decode($httpResponse->getBody());

        $sessionAccessKeyId = $respObj->Credentials->AccessKeyId;
        $sessionAccessKeySecret = $respObj->Credentials->AccessKeySecret;
        $securityToken = $respObj->Credentials->SecurityToken;

        return new Credential($sessionAccessKeyId, $sessionAccessKeySecret, $securityToken);
    }
}