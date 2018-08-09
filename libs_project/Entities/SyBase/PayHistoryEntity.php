<?php
namespace Entities\SyBase;

use DB\Entities\MysqlEntity;

class PayHistoryEntity extends MysqlEntity {
    public function __construct(string $dbName='') {
        $this->_dbName = isset($dbName{0}) ? $dbName : 'sy_base';
        parent::__construct($this->_dbName, 'pay_history','id');
    }

    /**
     *
     * @var int
     */
    public $id = null;

    /**
     * 交易类型
     * @var string
     */
    public $trade_type = '';

    /**
     * 交易单号
     * @var string
     */
    public $trade_sn = '';

    /**
     * 商户单号
     * @var string
     */
    public $seller_sn = '';

    /**
     * 应用ID
     * @var string
     */
    public $app_id = '';

    /**
     * 买家ID
     * @var string
     */
    public $buyer_id = '';

    /**
     * 交易金额,单位为分
     * @var int
     */
    public $money = 0;

    /**
     * 附加数据
     * @var string
     */
    public $attach = '';

    /**
     * 支付回调数据,json格式
     * @var string
     */
    public $content = '';

    /**
     * 交易状态
     * @var string
     */
    public $status = '';

    /**
     * 创建时间戳
     * @var int
     */
    public $created = 0;
}