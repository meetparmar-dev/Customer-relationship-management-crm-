<?php

namespace common\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            } elseif ($user->status !== User::STATUS_ACTIVE) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->getUser();

        // If 2FA enabled → generate OTP and stop login
        if ($user->twofa_enabled == 1) {

            $user->generateTwoFactorCode();
            $user->sendTwoFactorOtp();

            Yii::$app->session->set('2fa_user', $user->id);

            return false; // Stop normal login
        }

        // Normal login
        if (Yii::$app->user->login(
            $user,
            $this->rememberMe ? 3600 * 24 * 30 : 0
        )) {

            //Fire login event
            $user->trigger(User::EVENT_USER_LOGIN);

            return true;
        }

        return false;
    }




    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === false) {

            $this->_user = User::find()
                ->where(['username' => $this->username])
                ->orWhere(['email' => $this->username])
                ->one();
        }

        return $this->_user;
    }
}
