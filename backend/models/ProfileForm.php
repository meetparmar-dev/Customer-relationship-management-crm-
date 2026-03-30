<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * ProfileForm model
 *
 * This model is used to update the logged-in user's profile
 * including name, username, email, and avatar.
 */
class ProfileForm extends Model
{
    /* ================= BASIC INFO ================= */

    public $first_name;
    public $last_name;
    public $username;
    public $email;
    public $pending_email;
    public $avatarFile;
    public $current_password;
    public $new_password;
    public $confirm_password;


    /**
     * Scenarios
     *
     * Defines which attributes are active for profile update.
     */
    public function scenarios()
    {
        return [
            'profile' => ['first_name', 'last_name', 'username', 'email', 'avatarFile'],
            'default' => ['first_name', 'last_name', 'username', 'email', 'avatarFile'],
        ];
    }

    /**
     * Validation rules
     */
    public function rules()
    {
        return [

            /* ===== PROFILE FIELDS ===== */

            // Required fields
            [['first_name', 'last_name', 'username', 'email'], 'required'],

            // Trim input
            [['first_name', 'last_name', 'username', 'email'], 'trim'],

            // Name length
            [['first_name', 'last_name'], 'string', 'min' => 2, 'max' => 100],

            // Username validation
            ['username', 'string', 'min' => 3, 'max' => 50],
            ['username', 'validateUsername'],

            // Email validation
            ['email', 'email'],
            ['email', 'validateEmail'],

            /* ===== AVATAR UPLOAD ===== */

            [
                'avatarFile',
                'file',
                'skipOnEmpty' => true,
                'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
                'maxSize' => 2 * 1024 * 1024,
            ],
        ];
    }

    /**
     * Custom validation to check username uniqueness.
     */
    public function validateUsername($attribute)
    {
        $userId = Yii::$app->user->id;

        $exists = User::find()
            ->where(['username' => $this->$attribute])
            ->andWhere(['<>', 'id', $userId])
            ->exists();

        if ($exists) {
            $this->addError($attribute, 'This username is already taken.');
        }
    }

    /**
     * Custom validation to check email uniqueness.
     */
    public function validateEmail($attribute)
    {
        $userId = Yii::$app->user->id;

        $exists = User::find()
            ->where(['email' => $this->$attribute])
            ->andWhere(['<>', 'id', $userId])
            ->exists();

        if ($exists) {
            $this->addError($attribute, 'This email address is already in use.');
        }
    }

    /**
     * Attribute labels for form fields.
     */
    public function attributeLabels()
    {
        return [
            'first_name' => 'First Name',
            'last_name'  => 'Last Name',
            'username'   => 'Username',
            'email'      => 'Email Address',
            'avatarFile' => 'Profile Picture',
        ];
    }

    public function saveProfile(User $user)
    {
        $user->first_name = $this->first_name;
        $user->last_name  = $this->last_name;
        $user->username   = $this->username;

        return $user->save(false);
    }
}
