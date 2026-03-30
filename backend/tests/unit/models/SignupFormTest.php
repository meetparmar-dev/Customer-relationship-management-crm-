<?php

namespace backend\tests\unit;

use backend\models\SignupForm;
use common\models\User;
use Yii;

class SignupFormTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
        // Clean test users
        User::deleteAll(['like', 'email', '@test.com', false]);
    }

    private function getValidData()
    {
        return [
            'first_name' => 'Meet',
            'last_name' => 'Parmar',
            'username' => 'meet_' . time(),
            'email' => 'meet_' . time() . '@test.com',
            'password' => 'password123',
            'confirm_password' => 'password123',
        ];
    }

    /** ✅ Valid signup */
    public function testSignupWithValidData()
    {
        $model = new SignupForm();
        $model->load($this->getValidData(), '');

        $result = $model->signup();

        $this->assertTrue($result);

        $user = User::findOne(['email' => $model->email]);
        $this->assertNotNull($user);
        $this->assertEquals($model->first_name, $user->first_name);
        $this->assertEquals($model->last_name, $user->last_name);
        $this->assertEquals($model->username, $user->username);
        $this->assertEquals(0, $user->role);
        $this->assertNotEmpty($user->auth_key);
        $this->assertNotEmpty($user->verification_token);
    }

    /** ❌ Required fields */
    public function testRequiredFields()
    {
        $model = new SignupForm();
        $this->assertFalse($model->validate());

        $this->assertArrayHasKey('first_name', $model->errors);
        $this->assertArrayHasKey('last_name', $model->errors);
        $this->assertArrayHasKey('username', $model->errors);
        $this->assertArrayHasKey('email', $model->errors);
        $this->assertArrayHasKey('password', $model->errors);
    }

    /** ❌ Password mismatch */
    public function testPasswordMismatch()
    {
        $data = $this->getValidData();
        $data['confirm_password'] = 'wrongpass';

        $model = new SignupForm();
        $model->load($data, '');

        $this->assertFalse($model->validate());
        $this->assertArrayHasKey('confirm_password', $model->errors);
    }

    /** ❌ Duplicate email */
    public function testDuplicateEmail()
    {
        $data = $this->getValidData();

        // create existing user
        $user = new User();
        $user->username = 'existing_user';
        $user->email = $data['email'];
        $user->setPassword('password123');
        $user->generateAuthKey();
        $user->save(false);

        $model = new SignupForm();
        $model->load($data, '');

        $this->assertFalse($model->validate());
        $this->assertArrayHasKey('email', $model->errors);
    }

    /** ❌ Duplicate username */
    public function testDuplicateUsername()
    {
        $data = $this->getValidData();

        $user = new User();
        $user->username = $data['username'];
        $user->email = 'other@test.com';
        $user->setPassword('password123');
        $user->generateAuthKey();
        $user->save(false);

        $model = new SignupForm();
        $model->load($data, '');

        $this->assertFalse($model->validate());
        $this->assertArrayHasKey('username', $model->errors);
    }

    /** ❌ Invalid email */
    public function testInvalidEmail()
    {
        $data = $this->getValidData();
        $data['email'] = 'not-an-email';

        $model = new SignupForm();
        $model->load($data, '');

        $this->assertFalse($model->validate());
        $this->assertArrayHasKey('email', $model->errors);
    }
}
