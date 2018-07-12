<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/7/7 0007
 * Time: 16:04
 */
namespace SyIM;

use Constant\ErrorCode;
use Exception\IM\TencentException;

class TencentConfig {
    private $appId = '';
    private $privateKey = '';
    private $commandSign = '';

    public function __construct(){
    }

    private function __clone(){
    }

    /**
     * @return string
     */
    public function getAppId() : string {
        return $this->appId;
    }

    /**
     * @param string $appId 应用ID
     * @throws \Exception\IM\TencentException
     */
    public function setAppId(string $appId){
        if(ctype_alnum($appId)){
            $this->appId = $appId;
        } else {
            throw new TencentException('appid不合法', ErrorCode::IM_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getPrivateKey() : string {
        return $this->privateKey;
    }

    /**
     * @param string $privateKey 私钥文件
     * @throws \Exception\IM\TencentException
     */
    public function setPrivateKey(string $privateKey) {
        if(!file_exists($privateKey)){
            throw new TencentException('私钥文件必须存在', ErrorCode::IM_PARAM_ERROR);
        } else if(!is_file($privateKey)){
            throw new TencentException('私钥必须是文件', ErrorCode::IM_PARAM_ERROR);
        } else if(!is_readable($privateKey)){
            throw new TencentException('私钥文件必须可读', ErrorCode::IM_PARAM_ERROR);
        }

        $this->privateKey = $privateKey;
    }

    /**
     * @return string
     */
    public function getCommandSign() : string {
        return $this->commandSign;
    }

    /**
     * @param string $commandSign 签名命令文件
     * @throws \Exception\IM\TencentException
     */
    public function setCommandSign(string $commandSign) {
        if(!file_exists($commandSign)){
            throw new TencentException('签名命令文件必须存在', ErrorCode::IM_PARAM_ERROR);
        } else if(!is_file($commandSign)){
            throw new TencentException('签名命令必须是文件', ErrorCode::IM_PARAM_ERROR);
        } else if(!is_executable($commandSign)){
            throw new TencentException('签名命令文件必须可执行', ErrorCode::IM_PARAM_ERROR);
        }

        $this->commandSign = $commandSign;
    }
}