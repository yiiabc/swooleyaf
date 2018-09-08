<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/7 0007
 * Time: 9:42
 */
namespace TaoBao;

use DesignPatterns\Singletons\SmsConfigSingleton;

abstract class TaoBaoBase {
    /**
     * API接口名称
     * @var string
     */
    private $method = '';
    /**
     * 应用标识
     * @var string
     */
    private $appKey = '';
    /**
     * 签名的摘要算法
     * @var string
     */
    private $signMethod = '';
    /**
     * 响应格式
     * @var string
     */
    private $format = '';
    /**
     * API协议版本
     * @var string
     */
    private $version = '';
    /**
     * 时间戳
     * @var string
     */
    private $timestamp = '';
    /**
     * 响应标识
     * @var string
     */
    private $responseTag = '';

    public function __construct(){
        $this->appKey = SmsConfigSingleton::getInstance()->getDaYuConfig()->getAppKey();
        $this->signMethod = 'md5';
        $this->format = 'json';
        $this->version = '2.0';
        $this->timestamp = date('Y-m-d H:i:s');
    }

    private function __clone(){
    }

    /**
     * @param string $method
     */
    protected function setMethod(string $method) {
        $this->method = $method;
        $this->responseTag = str_replace('.', '_', $method) . '_response';
    }

    /**
     * @return string
     */
    public function getResponseTag() : string {
        return $this->responseTag;
    }

    public function getBaseDetail() : array {
        return [
            'v' => $this->version,
            'app_key' => $this->appKey,
            'sign_method' => $this->signMethod,
            'format' => $this->format,
            'method' => $this->method,
            'timestamp' => $this->timestamp,
        ];
    }

    abstract public function getDetail() : array;
}