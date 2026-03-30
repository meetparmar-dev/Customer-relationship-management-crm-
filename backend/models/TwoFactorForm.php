<?php

namespace backend\models;

use yii\base\Model;

class TwoFactorForm extends Model
{
    public $otp;

    public function rules()
    {
        return [
            [['otp'], 'required'],
            ['otp', 'string', 'length' => 6],
            ['otp', 'match', 'pattern' => '/^[0-9]{6}$/', 'message' => 'OTP must be 6 digits'],
        ];
    }
}
