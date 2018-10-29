<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/13 0013
 * Time: 7:47
 */
namespace Wx\OpenMini;

use Constant\ErrorCode;
use Tool\Tool;
use Wx\WxBaseOpenMini;
use Wx\WxUtilBase;
use Wx\WxUtilOpenBase;

class CodeAuditStatusLatest extends WxBaseOpenMini {
    /**
     * 应用ID
     * @var string
     */
    private $appId = '';

    public function __construct(string $appId){
        parent::__construct();
        $this->serviceUrl = 'https://api.weixin.qq.com/wxa/get_latest_auditstatus?access_token=';
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
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $sendData['errmsg'];
        }

        return $resArr;
    }
}