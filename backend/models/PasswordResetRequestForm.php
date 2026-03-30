<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * PasswordResetRequestForm model (Backend)
 *
 * This model handles password reset requests.
 * It validates the user email and sends a password reset link
 * to active users.
 */
class PasswordResetRequestForm extends Model
{
    /**
     * User email for password reset.
     *
     * @var string
     */
    public $email;

    /**
     * Validation rules.
     */
    public function rules()
    {
        return [
            // Trim extra spaces
            ['email', 'trim'],

            // Email is required
            ['email', 'required'],

            // Must be a valid email
            ['email', 'email'],

            // Email must exist for active users
            [
                'email',
                'exist',
                'targetClass' => User::class,
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with this email address.'
            ],
        ];
    }

    /**
     * Sends an email with password reset link.
     *
     * @return bool Whether the email was sent successfully
     */
    public function sendEmail()
    {
        // Find active user by email
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email'  => $this->email,
        ]);

        // Stop if user not found
        if (!$user) {
            return false;
        }

        // Generate new reset token if old one is invalid or expired
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();

            // Save user with new token
            if (!$user->save(false)) {
                return false;
            }
        }

        // Send password reset email
        return Yii::$app
            ->mailer
            ->compose(
                [
                    'html' => 'passwordResetToken-html',
                    'text' => 'passwordResetToken-text'
                ],
                [
                    'user' => $user // Pass user data to email templates
                ]
            )
            ->setFrom([
                Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'
            ])
            ->setTo($this->email)
            ->setSubject('Password reset for ' . Yii::$app->name)
            ->send();
    }
}
