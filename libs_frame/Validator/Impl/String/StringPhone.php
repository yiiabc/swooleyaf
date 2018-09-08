<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-3-26
 * Time: 1:09
 */
namespace Validator\Impl\String;

use Constant\Project;
use Validator\Base\BaseValidator;
use Validator\ValidatorService;

class StringPhone extends BaseValidator implements ValidatorService {
    public function __construct() {
        parent::__construct();
        $this->validatorType = Project::VALIDATOR_STRING_TYPE_PHONE;
    }

    private function __clone() {
    }

    public function validator($data, $compareData) : string {
        if ($data === null) {
            return '';
        }

        $trueData = $this->verifyStringData($data);
        if ($trueData === null) {
            return '必须是字符串';
        } else if((strlen($trueData) == 0) && !$compareData){
            return '';
        } else if(ctype_digit($trueData) && (strlen($trueData) == 11) && ($trueData{0} == '1')){
            return '';
        } else {
            return '格式必须是手机号码';
        }
    }
}