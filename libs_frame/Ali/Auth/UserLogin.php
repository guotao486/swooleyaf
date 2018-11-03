<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/11/1 0001
 * Time: 9:40
 */

namespace Ali\Auth;

use Ali\AliBase;
use Constant\ErrorCode;
use DesignPatterns\Singletons\AliConfigSingleton;
use Exception\Ali\AliAuthException;

class UserLogin extends AliBase {
    /**
     * 授权类型
     * @var array
     */
    private $scopes = [];
    /**
     * 校验码
     * @var string
     */
    private $state = '';

    public function __construct(string $appId){
        parent::__construct($appId);
        $payConfig = AliConfigSingleton::getInstance()->getPayConfig($appId);
        $this->return_baseurl = $payConfig->getUrlReturn();
        $this->setMethod('alipay.user.info.auth');
    }

    private function __clone(){
    }

    /**
     * @param string $scope
     * @throws \Exception\Ali\AliAuthException
     */
    public function setScopes(string $scope){
        if(in_array($scope, ['auth_user', 'auth_base',])){
            $this->biz_content['scopes'] = [
                $scope,
            ];
        } else {
            throw new AliAuthException('授权类型不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $state
     * @throws \Exception\Ali\AliAuthException
     */
    public function setState(string $state){
        if(ctype_alnum($state) && (strlen($state) <= 100)){
            $this->biz_content['state'] = $state;
        } else {
            throw new AliAuthException('校验码不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(!isset($this->biz_content['scopes'])){
            throw new AliAuthException('授权类型不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if(!isset($this->biz_content['state'])){
            throw new AliAuthException('校验码不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        return $this->getContent();
    }
}