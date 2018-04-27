<?php
namespace Entities\SyBase;

use DB\Entities\MysqlEntity;

class WxshopConfigEntity extends MysqlEntity {
    public function __construct() {
        parent::__construct('sy_base', 'wxshop_config','id');
    }

    /**
     * 主键ID
     * @var int
     */
    public $id = null;

    /**
     * 公众号app id
     * @var string
     */
    public $app_id = '';

    /**
     * 公众号密钥
     * @var string
     */
    public $app_secret = '';

    /**
     * 公众号客户端ip
     * @var string
     */
    public $app_clientip = '';

    /**
     * 公众号模板列表
     * @var string
     */
    public $app_templates = '';

    /**
     * 公众号商户号
     * @var string
     */
    public $pay_mchid = '';

    /**
     * 公众号支付密钥
     * @var string
     */
    public $pay_key = '';

    /**
     * 公众号支付授权URL
     * @var string
     */
    public $payurl_auth = '';

    /**
     * 公众号支付异步通知URL
     * @var string
     */
    public $payurl_notify = '';

    /**
     * 公众号商户证书内容，去除证书文件的第一行和最后一行以及所有换行
     * @var string
     */
    public $payssl_cert = '';

    /**
     * 公众号商户密钥内容，去除密钥文件的第一行和最后一行以及所有换行
     * @var string
     */
    public $payssl_key = '';

    /**
     * 状态
     * @var int
     */
    public $status = 1;

    /**
     * 创建时间戳
     * @var int
     */
    public $created = 0;

    /**
     * 修改时间戳
     * @var int
     */
    public $updated = 0;
}