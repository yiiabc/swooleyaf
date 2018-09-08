<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2018/9/7 0007
 * Time: 10:14
 */
namespace TaoBao\Communication;

use Constant\ErrorCode;
use Exception\Sms\AliDaYuException;
use TaoBao\TaoBaoBase;
use Tool\Tool;

class AliDaYuSmsSend extends TaoBaoBase {
    /**
     * 短信类型
     * @var string
     */
    private $smsType = '';
    /**
     * 接收手机号码列表
     * @var array
     */
    private $recNumList = [];
    /**
     * 签名名称
     * @var string
     */
    private $signName = '';
    /**
     * 模板ID
     * @var string
     */
    private $templateCode = '';
    /**
     * 模板参数
     * @var array
     */
    private $smsParams = [];
    /**
     * @var array
     */
    private $badSmsSignNames = [];

    public function __construct() {
        parent::__construct();
        $this->reqData['sms_type'] = 'normal';
        $this->badSmsSignNames = [
            '大鱼测试',
            '活动验证',
            '变更验证',
            '登录验证',
            '注册验证',
            '身份验证',
        ];
        $this->setMethod('alibaba.aliqin.fc.sms.num.send');
    }

    private function __clone(){
    }

    /**
     * @param array $recNumList
     * @throws \Exception\Sms\AliDaYuException
     */
    public function setRecNumList(array $recNumList) {
        if(empty($recNumList)){
            throw new AliDaYuException('接收号码不能为空', ErrorCode::SMS_PARAM_ERROR);
        } else if(count($recNumList) > 200){
            throw new AliDaYuException('接收号码不能超过200个', ErrorCode::SMS_PARAM_ERROR);
        }

        foreach ($recNumList as $eRecNum) {
            if(ctype_digit($eRecNum) && (strlen($eRecNum) == 11) && ($eRecNum{0} == '1')){
                $this->recNumList[] = $eRecNum;
            } else {
                throw new AliDaYuException('接收号码不合法', ErrorCode::SMS_PARAM_ERROR);
            }
        }
        array_unique($this->recNumList);
        $this->reqData['rec_num'] = implode(',', $this->recNumList);
    }

    /**
     * @param string $signName
     * @throws \Exception\Sms\AliDaYuException
     */
    public function setSignName(string $signName) {
        if (strlen($signName) == 0) {
            throw new AliDaYuException('签名名称不能为空', ErrorCode::SMS_PARAM_ERROR);
        } else if (in_array($signName, $this->badSmsSignNames)) {
            throw new AliDaYuException('签名名称不能为系统默认签名', ErrorCode::SMS_PARAM_ERROR);
        }

        $this->reqData['sms_free_sign_name'] = $signName;
    }

    /**
     * @param string $templateId
     * @throws \Exception\Sms\AliDaYuException
     */
    public function setTemplateId(string $templateId) {
        if (strlen($templateId) > 0) {
            $this->reqData['sms_template_code'] = $templateId;
        } else {
            throw new AliDaYuException('模板ID不能为空', ErrorCode::SMS_PARAM_ERROR);
        }
    }

    /**
     * @param array $params
     */
    public function setSmsParams(array $params) {
        if(!empty($params)){
            $this->reqData['sms_param'] = Tool::jsonEncode($params, JSON_UNESCAPED_UNICODE);
        }
    }

    public function getDetail() : array {
        if (!isset($this->reqData['rec_num'])) {
            throw new AliDaYuException('接收号码不能为空', ErrorCode::SMS_PARAM_ERROR);
        }
        if (!isset($this->reqData['sms_free_sign_name'])) {
            throw new AliDaYuException('签名名称不能为空', ErrorCode::SMS_PARAM_ERROR);
        }
        if (!isset($this->reqData['sms_template_code'])) {
            throw new AliDaYuException('模板ID不能为空', ErrorCode::SMS_PARAM_ERROR);
        }

        return $this->getContent();
    }
}