<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/13 0013
 * Time: 8:11
 */
namespace Wx2\OpenMini;

use Constant\ErrorCode;
use Tool\Tool;
use Wx2\WxBaseOpenMini;
use Wx2\WxUtilBase;
use Wx2\WxUtilOpenBase;

class CodeRollback extends WxBaseOpenMini {
    /**
     * 应用ID
     * @var string
     */
    private $appId = '';

    public function __construct(string $appId){
        parent::__construct();
        $this->serviceUrl = 'https://api.weixin.qq.com/wxa/revertcoderelease?access_token=';
        $this->appId = $appId;
    }

    public function __clone(){
    }

    public function getDetail() : array {
        $resArr = [
            'code' => 0,
        ];

        $this->curlConfigs[CURLOPT_URL] = $this->serviceUrl . WxUtilOpenBase::getAuthorizerAccessToken($this->appId);
        $sendRes = WxUtilBase::sendGetReq($this->curlConfigs);
        $sendData = Tool::jsonDecode($sendRes);
        if($sendData['errcode'] == 0){
            $resArr['data'] = $sendData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_GET_ERROR;
            $resArr['message'] = $sendData['errmsg'];
        }

        return $resArr;
    }
}