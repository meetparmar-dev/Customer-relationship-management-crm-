<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $first_name;
    public $last_name;
    public $username;
    public $email;
    public $password;
    public $confirm_password;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            /* ================= NAME ================= */
            [['first_name', 'last_name'], 'required'],
            [['first_name', 'last_name'], 'trim'],
            [['first_name', 'last_name'], 'string', 'min' => 2, 'max' => 100],

            /* ================= USERNAME ================= */
            ['username', 'required'],
            ['username', 'trim'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            [
                'username',
                'unique',
                'targetClass' => User::class,
                'message' => 'This username has already been taken.'
            ],

            /* ================= EMAIL ================= */
            ['email', 'required'],
            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            [
                'email',
                'unique',
                'targetClass' => User::class,
                'message' => 'This email address has already been taken.'
            ],

            /* ================= PASSWORD ================= */
            [['password', 'confirm_password'], 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],

            /* ================= CONFIRM PASSWORD ================= */
            [
                'confirm_password',
                'compare',
                'compareAttribute' => 'password',
                'message' => 'Passwords do not match.'
            ],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool|null
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->first_name = $this->first_name;
        $user->last_name  = $this->last_name;
        $user->username   = $this->username;
        $user->email      = $this->email;
        $user->role       = 0;

        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();

        return $user->save() && $this->sendEmail($user);
    }

    /**
     * Sends confirmation email to user
     */
    protected function sendEmail($user)
    {
        return Yii::$app->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
