<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;




/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{

    const EVENT_AFTER_REGISTER = 'afterRegister';
    const EVENT_EMAIL_CHANGE_REQUESTED = 'emailChangeRequested';
    const EVENT_PASSWORD_CHANGED = 'passwordChanged';
    const EVENT_2FA_CHANGED = 'twoFactorChanged';
    const EVENT_USER_CREATED = 'userCreated';
    const EVENT_EMAIL_CHANGED_BY_ADMIN = 'emailChangedByAdmin';
    const EVENT_ROLE_CHANGED          = 'roleChanged';
    const EVENT_STATUS_CHANGED        = 'statusChanged';
    const EVENT_USER_DELETED = 'userDeleted';
    const EVENT_USER_LOGIN = 'userLogin';
    const EVENT_USER_LOGOUT = 'userLogout';





    public $password;
    public $avatarFile;
    public $confirm_password;
    public $old_email;





    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    const ROLE_USER  = 0;
    const ROLE_ADMIN = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            /* ================= BASIC INFO ================= */
            [['first_name', 'last_name', 'username', 'email'], 'required'],
            [['first_name', 'last_name', 'username', 'email'], 'trim'],

            [['first_name', 'last_name'], 'string', 'min' => 2, 'max' => 100],
            ['username', 'string', 'min' => 3, 'max' => 50],

            ['email', 'email'],
            ['email', 'string', 'max' => 255],

            /* ================= UNIQUE ================= */
            ['username', 'unique'],
            ['email', 'unique'],


            /* ================= ROLE & STATUS ================= */
            ['role', 'required'],
            ['role', 'in', 'range' => [self::ROLE_USER, self::ROLE_ADMIN]],

            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [
                self::STATUS_ACTIVE,
                self::STATUS_INACTIVE,
                self::STATUS_DELETED
            ]],

            /* ================= PASSWORD (ONLY IF SET) ================= */
            [['password', 'confirm_password'], 'required', 'on' => 'create'],
            ['password', 'string', 'min' => 6],
            [
                'confirm_password',
                'compare',
                'compareAttribute' => 'password',
                'message' => 'Passwords do not match.'
            ],

            /* ================= AVATAR ================= */
            [
                'avatarFile',
                'file',
                'skipOnEmpty' => true,
                'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
                'maxSize' => 2 * 1024 * 1024,
            ],

            /* ================= 2FA ================= */
            ['twofa_enabled', 'boolean'],
            ['twofa_secret', 'string'],
            ['twofa_expires', 'integer'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'password' => 'Password',
            'confirm_password' => 'Confirm Password',
            'twofa_enabled' => 'Enable Two-Factor Authentication',
            'twofa_verification_code' => 'Verification Code',
        ];
    }


    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    /**
     * JWT token verification
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        try {
            $decoded = JWT::decode(
                $token,
                new Key(Yii::$app->params['jwtSecret'], 'HS256')
            );

            if (empty($decoded->uid) || empty($decoded->jti)) {
                return null;
            }

            $user = self::findOne([
                'id'     => $decoded->uid,
                'status' => self::STATUS_ACTIVE,
            ]);

            // 🔐 jti mismatch = invalid token
            if (!$user || $user->jwt_id !== $decoded->jti) {
                return null;
            }

            return $user;
        } catch (\Throwable $e) {
            return null;
        }
    }


    /**
     * Generate JWT token
     */
    public function generateJwt()
    {
        $now = time();

        // 🔐 unique token id
        $jti = Yii::$app->security->generateRandomString(32);

        $payload = [
            'iss'  => 'crm-api',
            'aud'  => 'crm-client',
            'iat'  => $now,
            'nbf'  => $now,
            'exp'  => $now + 3600,
            'uid'  => $this->id,
            'role' => $this->role,
            'jti'  => $jti, // ✅ IMPORTANT
        ];

        // ✅ DB me sirf jti store
        $this->jwt_id = $jti;
        $this->save(false);

        return JWT::encode(
            $payload,
            Yii::$app->params['jwtSecret'],
            'HS256'
        );
    }


    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setNewPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function isUser()
    {
        return $this->role == self::ROLE_USER;
    }

    public function isAdmin()
    {
        return $this->role == self::ROLE_ADMIN;
    }

    public function getFullName()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function init()
    {
        parent::init();

        // Existing welcome email
        $this->on(
            self::EVENT_AFTER_REGISTER,
            ['common\listeners\UserEventListener', 'sendWelcomeEmail']
        );

        // New email change verification
        $this->on(
            self::EVENT_EMAIL_CHANGE_REQUESTED,
            ['common\listeners\UserEmailChangeListener', 'sendVerification']
        );

        // 🔐 Password changed event
        $this->on(
            self::EVENT_PASSWORD_CHANGED,
            ['common\listeners\PasswordChangedListener', 'handle']
        );

        // 🔐 2FA event
        $this->on(self::EVENT_2FA_CHANGED, ['common\listeners\TwoFactorChangedListener', 'handle']);

        $this->on(
            self::EVENT_USER_CREATED,
            ['common\listeners\UserEventListener', 'onUserCreated']
        );

        $this->on(self::EVENT_EMAIL_CHANGED_BY_ADMIN, ['common\listeners\AdminUserChangeListener', 'onEmailChange']);
        $this->on(self::EVENT_ROLE_CHANGED, ['common\listeners\AdminUserChangeListener', 'onRoleChange']);
        $this->on(self::EVENT_STATUS_CHANGED, ['common\listeners\AdminUserChangeListener', 'onStatusChange']);

        $this->on(self::EVENT_USER_DELETED, ['common\listeners\AdminUserChangeListener', 'onUserDelete']);

        // 🔐 Login
        $this->on(self::EVENT_USER_LOGIN, ['common\listeners\UserEventListener', 'onUserLogin']);

        // 🔐 Logout
        $this->on(self::EVENT_USER_LOGOUT, ['common\listeners\UserEventListener', 'onUserLogout']);
    }



    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // 🆕 User created
        if ($insert) {
            $this->trigger(self::EVENT_USER_CREATED);

            if ($this->status == self::STATUS_ACTIVE) {
                $this->trigger(self::EVENT_AFTER_REGISTER);
            }

            return;
        }

        // ✏️ Email changed by admin
        if (array_key_exists('email', $changedAttributes)) {
            $this->trigger(self::EVENT_EMAIL_CHANGED_BY_ADMIN);
        }

        // 🔐 Role changed
        if (array_key_exists('role', $changedAttributes)) {
            $this->trigger(self::EVENT_ROLE_CHANGED);
        }

        // 🚦 Status changed
        if (array_key_exists('status', $changedAttributes)) {
            $this->trigger(self::EVENT_STATUS_CHANGED);
        }
    }


    public function afterDelete()
    {
        parent::afterDelete();

        // 🔔 User deleted (admin / api / console / web)
        $this->trigger(self::EVENT_USER_DELETED);
    }

    public function onSuccessfulLogin()
    {
        // Cleanup OTP if any
        $this->clearTwoFactorCode();

        // Fire login event
        $this->trigger(self::EVENT_USER_LOGIN);
    }


    public function onSuccessfulLogout()
    {
        // 🔐 Fire logout event (audit + security)
        $this->trigger(self::EVENT_USER_LOGOUT);
    }



    /**
     * Sends welcome email to user
     *
     * @return bool whether the email was sent
     */

    public static function generateOtpCode($length = 6)
    {
        $min = 10 ** ($length - 1);
        $max = (10 ** $length) - 1;

        return (string) random_int($min, $max);
    }

    // Generate OTP
    public function generateTwoFactorCode()
    {
        $this->twofa_secret  = self::generateOtpCode(6);
        $this->twofa_expires = time() + 300;

        return $this->save(false);
    }

    // Verify OTP
    public function validateTwoFactorCode($code)
    {
        if (!$this->twofa_secret || !$this->twofa_expires) {
            return false;
        }

        if (time() > $this->twofa_expires) {
            return false;
        }

        return $this->twofa_secret === $code;
    }

    // Clear OTP after success
    public function clearTwoFactorCode()
    {
        $this->twofa_secret = null;
        $this->twofa_expires = null;
        $this->save(false);
    }

    public function sendTwoFactorOtp()
    {
        return Yii::$app->mailer
            ->compose('twofa-otp-html', [
                'user' => $this
            ])
            ->setFrom([Yii::$app->params['supportEmail'] => 'CRM System'])
            ->setTo($this->email)
            ->setSubject('Your Login OTP')
            ->send();
    }

    public function requestEmailChange($newEmail)
    {
        $this->pending_email = $newEmail;
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();

        if (!$this->save(false)) {
            return false;
        }

        // Fire event
        $this->trigger(self::EVENT_EMAIL_CHANGE_REQUESTED);

        return true;
    }

    public function confirmEmailChange()
    {
        $this->email = $this->pending_email;
        $this->pending_email = null;
        $this->verification_token = null;
        return $this->save(false);
    }


    public function enable2fa()
    {
        $this->twofa_enabled = 1;

        if ($this->save(false)) {
            $this->trigger(self::EVENT_2FA_CHANGED);
            return true;
        }

        return false;
    }

    public function disable2fa()
    {
        $this->twofa_enabled = 0;

        if ($this->save(false)) {
            $this->trigger(self::EVENT_2FA_CHANGED);
            return true;
        }

        return false;
    }
}
