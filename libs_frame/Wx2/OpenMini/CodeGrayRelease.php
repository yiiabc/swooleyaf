<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/13 0013
 * Time: 8:28
 */
namespace Wx2\OpenMini;

use Constant\ErrorCode;
use Exception\Wx\WxOpenException;
use Tool\Tool;
use Wx2\WxBaseOpenMini;
use Wx2\WxUtilBase;
use Wx2\WxUtilOpenBase;

class CodeGrayRelease extends WxBaseOpenMini {
    /**
     * 应用ID
     * @var string
     */
    private $appId = '';
    /**
     * 灰度百分比
     * @var int
     */
    private $percentage = 0;

    public function __construct(string $appId){
        parent::__construct();
        $this->serviceUrl = 'https://api.weixin.qq.com/wxa/grayrelease?access_token=';
        $this->appId = $appId;
    }

    public function __clone(){
    }

    /**
     * @param int $percentage
     * @throws \Exception\Wx\WxOpenException
     */
    public function setPercentage(int $percentage){
        if (($percentage > 0) && ($percentage <= 100)) {
            $this->reqData['gray_percentage'] = $percentage;
        } else {
            throw new WxOpenException('灰度百分比不合法', ErrorCode::WXOPEN_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(!isset($this->reqData['gray_percentage'])){
            throw new WxOpenException('灰度百分比不能为空', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        $resArr = [
            'code' => 0,
        ];

        $this->curlConfigs[CURLOPT_URL] = $this->serviceUrl . WxUtilOpenBase::getAuthorizerAccessToken($this->appId);
        $this->curlConfigs[CURLOPT_POSTFIELDS] = Tool::jsonEncode($this->reqData, JSON_UNESCAPED_UNICODE);
        $this->curlConfigs[CURLOPT_SSL_VERIFYPEER] = false;
        $this->curlConfigs[CURLOPT_SSL_VERIFYHOST] = false;
        $sendRes = WxUtilBase::sendPostReq($this->curlConfigs);
        $sendData = Tool::jsonDecode($sendRes);
        if($sendData['errcode'] == 0){
            $resArr['data'] = $sendData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $sendData['errmsg'];
        }

        return $resArr;
    }
}