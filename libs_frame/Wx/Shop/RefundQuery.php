<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-03
 * Time: 23:08
 */
namespace Wx\Shop;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;
use Tool\Tool;
use Wx\WxUtilShop;

class RefundQuery extends ShopBase {
    public function __construct(string $appId) {
        parent::__construct();
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->appid = $shopConfig->getAppId();
        $this->mch_id = $shopConfig->getPayMchId();
        $this->sign_type = 'MD5';
        $this->nonce_str = Tool::createNonceStr(32, 'numlower');
    }

    private function __clone(){
    }

    /**
     * 公众账号ID
     * @var string
     */
    private $appid = '';

    /**
     * 商户号
     * @var string
     */
    private $mch_id = '';

    /**
     * 设备号
     * @var string
     */
    private $device_info = '';

    /**
     * 随机字符串
     * @var string
     */
    private $nonce_str = '';

    /**
     * 签名类型
     * @var string
     */
    private $sign_type = '';

    /**
     * 微信订单号
     * @var string
     */
    private $transaction_id = '';

    /**
     * 商户订单号
     * @var string
     */
    private $out_trade_no = '';

    /**
     * 微信退款单号
     * @var string
     */
    private $refund_id = '';

    /**
     * 商户退款单号
     * @var string
     */
    private $out_refund_no = '';

    /**
     * @param string $device_info
     */
    public function setDeviceInfo(string $device_info) {
        $this->device_info = $device_info;
    }

    /**
     * @param string $transactionId
     * @throws \Exception\Wx\WxException
     */
    public function setTransactionId(string $transactionId) {
        if (preg_match('/^4[0-9]{27}$/', $transactionId) > 0) {
            $this->transaction_id = $transactionId;
        } else {
            throw new WxException('微信订单号不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $outTradeNo
     * @throws \Exception\Wx\WxException
     */
    public function setOutTradeNo(string $outTradeNo) {
        if (preg_match('/^[a-zA-Z0-9]{1,32}$/', $outTradeNo) > 0) {
            $this->out_trade_no = $outTradeNo;
        } else {
            throw new WxException('商户订单号不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $refundId
     * @throws \Exception\Wx\WxException
     */
    public function setRefundId(string $refundId) {
        if (preg_match('/^[0-9]{28}$/', $refundId) > 0) {
            $this->refund_id = $refundId;
        } else {
            throw new WxException('微信退款单号不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $outRefundNo
     * @throws \Exception\Wx\WxException
     */
    public function setOutRefundNo(string $outRefundNo) {
        if (preg_match('/^[a-zA-Z0-9]{1,32}$/', $outRefundNo) > 0) {
            $this->out_refund_no = $outRefundNo;
        } else {
            throw new WxException('商户退款单号不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        $resArr = [];
        if(strlen($this->transaction_id) > 0){
            $resArr['transaction_id'] = $this->transaction_id;
        } else if(strlen($this->out_trade_no) > 0){
            $resArr['out_trade_no'] = $this->out_trade_no;
        } else if(strlen($this->refund_id) > 0){
            $resArr['refund_id'] = $this->refund_id;
        } else if(strlen($this->out_refund_no) > 0){
            $resArr['out_refund_no'] = $this->out_refund_no;
        } else {
            throw new WxException('微信订单号,商户订单号,微信退款单号,商户退款单号必须设置其中一个', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr['appid'] = $this->appid;
        $resArr['mch_id'] = $this->mch_id;
        $resArr['nonce_str'] = $this->nonce_str;
        $resArr['sign_type'] = $this->sign_type;
        if(strlen($this->device_info) > 0){
            $resArr['device_info'] = $this->device_info;
        }
        $resArr['sign'] = WxUtilShop::createSign($resArr, $this->appid);

        return $resArr;
    }
}