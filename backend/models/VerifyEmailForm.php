<?php

namespace backend\models;

use common\models\User;
use yii\base\InvalidArgumentException;
use yii\base\Model;

class VerifyEmailForm extends Model
{
    /**
     * @var string
     */
    public $token;

    /**
     * @var User
     */
    private $_user;


    /**
     * Creates a form model with given token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws InvalidArgumentException if token is empty or not valid
     */
    public function __construct($token, array $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException('Verify email token cannot be blank.');
        }
        // Find user by verification token regardless of status to handle edge cases
        $this->_user = User::findOne(['verification_token' => $token]);
        if (!$this->_user) {
            throw new InvalidArgumentException('Wrong verify email token.');
        }
        parent::__construct($config);
    }

    /**
     * Verify email
     *
     * @return User|null the saved model or null if saving fails
     */
    public function verifyEmail()
    {
        $user = $this->_user;

        if ($user->status === User::STATUS_ACTIVE) {
            return null;
        }

        $user->status = User::STATUS_ACTIVE;
        $user->verification_token = null;

        return $user->save(false) ? $user : null;
    }
}
