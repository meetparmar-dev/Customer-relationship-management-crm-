<?php

namespace backend\models;

use yii\base\Model;
use common\models\User;

class ChangePasswordForm extends Model
{
    public $current_password;
    public $new_password;
    public $confirm_password;

    public function rules()
    {
        return [
            [['current_password', 'new_password', 'confirm_password'], 'required'],
            ['new_password', 'string', 'min' => 6],
            ['confirm_password', 'compare', 'compareAttribute' => 'new_password'],
        ];
    }

    public function changePassword(User $user)
    {
        if (!$user->validatePassword($this->current_password)) {
            return false;
        }

        $user->setPassword($this->new_password);
        $user->generateAuthKey();
        $user->save(false);

        //FIRE EVENT
        $user->trigger(User::EVENT_PASSWORD_CHANGED);

        return true;
    }
}
