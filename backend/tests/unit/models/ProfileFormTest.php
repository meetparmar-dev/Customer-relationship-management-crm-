<?php

namespace backend\tests\unit\models;

use backend\models\ProfileForm;
use common\models\User;
use Codeception\Test\Unit;
use Yii;

class ProfileFormTest extends Unit
{
    protected function _before()
    {
        $user = new User();
        $user->username = 'testuser';
        $user->email = 'testuser@test.com';
        $user->setPassword('password123');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->save(false);

        Yii::$app->user->login($user);
    }

    public function testValidProfileData()
    {
        $model = new ProfileForm();
        $model->scenario = 'profile';

        $model->first_name = 'Meet';
        $model->last_name  = 'Parmar';
        $model->username   = 'meet123';
        $model->email      = 'meet@test.com';

        $this->assertTrue($model->validate());
        $this->assertFalse($model->hasErrors());
    }

    public function testRequiredFields()
    {
        $model = new ProfileForm();
        $model->scenario = 'profile';

        $model->validate();

        $this->assertTrue($model->hasErrors('first_name'));
        $this->assertTrue($model->hasErrors('last_name'));
        $this->assertTrue($model->hasErrors('username'));
        $this->assertTrue($model->hasErrors('email'));
    }

    public function testDuplicateUsername()
    {
        $existing = new User();
        $existing->username = 'duplicate_user';
        $existing->email = 'duplicate@test.com';
        $existing->setPassword('pass123');
        $existing->generateAuthKey();
        $existing->status = User::STATUS_ACTIVE;
        $existing->save(false);

        $model = new ProfileForm();
        $model->first_name = 'Test';
        $model->last_name = 'User';
        $model->username = 'duplicate_user';
        $model->email = 'new@test.com';

        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('username'));
    }

    public function testDuplicateEmail()
    {
        $existing = new User();
        $existing->username = 'unique_user';
        $existing->email = 'exists@test.com';
        $existing->setPassword('pass123');
        $existing->generateAuthKey();
        $existing->status = User::STATUS_ACTIVE;
        $existing->save(false);

        $model = new ProfileForm();
        $model->first_name = 'Test';
        $model->last_name = 'User';
        $model->username = 'newuser';
        $model->email = 'exists@test.com';

        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('email'));
    }

    public function testInvalidEmail()
    {
        $model = new ProfileForm();
        $model->first_name = 'Test';
        $model->last_name  = 'User';
        $model->username   = 'testuser2';
        $model->email      = 'invalid-email';

        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('email'));
    }

    public function testShortUsername()
    {
        $model = new ProfileForm();
        $model->first_name = 'Test';
        $model->last_name  = 'User';
        $model->username   = 'ab';
        $model->email      = 'ok@test.com';

        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('username'));
    }
}
