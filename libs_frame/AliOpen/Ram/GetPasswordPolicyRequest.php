<?php
namespace AliOpen\Ram;

use AliOpen\Core\RpcAcsRequest;

class GetPasswordPolicyRequest extends RpcAcsRequest {
    public function __construct(){
        parent::__construct("Ram", "2015-05-01", "GetPasswordPolicy");
        $this->setProtocol("https");
        $this->setMethod("POST");
    }
}