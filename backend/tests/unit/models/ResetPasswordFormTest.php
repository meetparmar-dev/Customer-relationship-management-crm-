<?php

namespace backend\tests\unit;

use backend\models\ResetPasswordForm;
use common\models\User;
use yii\base\InvalidArgumentException;
use Yii;

class ResetPasswordFormTest extends \Codeception\Test\Unit
{
    private function createUserWithToken()
    {
        $user = new User();
        $user->username = 'reset_test_' . time();
        $user->email = 'reset' . time() . '@test.com';
        $user->setPassword('oldpassword123');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->save(false);

        // generate reset token
        $user->generatePasswordResetToken();
        $user->save(false);

        return $user;
    }

    public function testResetPasswordWithValidToken()
    {
        $user = $this->createUserWithToken();

        $form = new ResetPasswordForm($user->password_reset_token);
        $form->password = 'newpassword123';

        $this->assertTrue($form->resetPassword());

        $user->refresh();

        // Token should be removed
        $this->assertNull($user->password_reset_token);

        // Password should be changed
        $this->assertTrue(Yii::$app->security->validatePassword('newpassword123', $user->password_hash));
    }

    public function testResetPasswordWithInvalidToken()
    {
        $this->expectException(InvalidArgumentException::class);
        new ResetPasswordForm('invalid_token_here');
    }

    public function testResetPasswordWithEmptyToken()
    {
        $this->expectException(InvalidArgumentException::class);
        new ResetPasswordForm('');
    }

    public function testPasswordIsRequired()
    {
        $user = $this->createUserWithToken();

        $form = new ResetPasswordForm($user->password_reset_token);
        $form->password = '';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('password', $form->getErrors());
    }
}
