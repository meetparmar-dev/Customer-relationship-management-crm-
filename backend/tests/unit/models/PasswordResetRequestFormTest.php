<?php

namespace backend\tests\unit\models;

use backend\models\PasswordResetRequestForm;
use common\models\User;
use Yii;

class PasswordResetRequestFormTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
        User::deleteAll();
    }

    public function testRules()
    {
        $model = new PasswordResetRequestForm();

        // Test required validation
        $model->email = '';
        $this->assertFalse($model->validate(['email']));
        $this->assertArrayHasKey('email', $model->getErrors());

        // Test email format validation
        $model->email = 'invalid-email';
        $this->assertFalse($model->validate(['email']));
        $this->assertArrayHasKey('email', $model->getErrors());

        // Test email existence validation
        $model->email = 'nonexistent@example.com';
        $this->assertFalse($model->validate(['email']));
        $this->assertArrayHasKey('email', $model->getErrors());

        // Test valid email that exists
        $user = $this->createActiveUser();
        $model->email = $user->email;
        $this->assertTrue($model->validate(['email']));
    }

    public function testSendEmailSuccessfully()
    {
        // Create an active user
        $user = $this->createActiveUser();

        $model = new PasswordResetRequestForm();
        $model->email = $user->email;

        // Test that validation passes
        $this->assertTrue($model->validate());

        // Mock the mailer to check if email sending is attempted
        $messageMock = $this->getMockBuilder(\yii\symfonymailer\Message::class)
            ->disableOriginalConstructor()
            ->getMock();

        $messageMock->expects($this->once())
            ->method('setFrom')
            ->willReturnSelf();

        $messageMock->expects($this->once())
            ->method('setTo')
            ->with($user->email)
            ->willReturnSelf();

        $messageMock->expects($this->once())
            ->method('setSubject')
            ->willReturnSelf();

        $messageMock->expects($this->once())
            ->method('send')
            ->willReturn(true);

        $mailerMock = $this->getMockBuilder(\yii\symfonymailer\Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mailerMock->expects($this->once())
            ->method('compose')
            ->willReturn($messageMock);

        // Replace the mailer component in the application
        Yii::$app->set('mailer', $mailerMock);

        $result = $model->sendEmail();
        $this->assertTrue($result);
    }

    public function testSendEmailWithInactiveUser()
    {
        // Create an inactive user
        $user = $this->createInactiveUser();

        $model = new PasswordResetRequestForm();
        $model->email = $user->email;

        // Validation should fail because user is not active
        $this->assertFalse($model->validate());
        $this->assertArrayHasKey('email', $model->getErrors());
    }

    public function testSendEmailWithNonExistingUser()
    {
        $model = new PasswordResetRequestForm();
        $model->email = 'nonexistent@example.com';

        // Validation should fail because user doesn't exist
        $this->assertFalse($model->validate());
        $this->assertArrayHasKey('email', $model->getErrors());
    }

    public function testSendEmailReturnsFalseWhenUserNotFound()
    {
        // Create an active user
        $user = $this->createActiveUser();

        $model = new PasswordResetRequestForm();
        $model->email = $user->email;

        // Temporarily delete the user to simulate race condition
        $user->delete();

        $result = $model->sendEmail();
        $this->assertFalse($result);
    }

    public function testSendEmailWithInvalidEmail()
    {
        $model = new PasswordResetRequestForm();
        $model->email = 'invalid-email-format';

        $this->assertFalse($model->validate(['email']));
    }

    /**
     * Helper method to create an active user
     */
    private function createActiveUser()
    {
        $user = new User();
        $user->username = 'testuser_' . time();
        $user->email = 'test_' . time() . '@example.com';
        $user->first_name = 'Test';
        $user->last_name = 'User';
        $user->status = User::STATUS_ACTIVE;
        $user->role = User::ROLE_USER;
        $user->setPassword('password_123');
        $user->generateAuthKey();
        $user->save(false);

        return $user;
    }

    /**
     * Helper method to create an inactive user
     */
    private function createInactiveUser()
    {
        $user = new User();
        $user->username = 'inactive_user_' . time();
        $user->email = 'inactive_' . time() . '@example.com';
        $user->first_name = 'Inactive';
        $user->last_name = 'User';
        $user->status = User::STATUS_INACTIVE;
        $user->role = User::ROLE_USER;
        $user->setPassword('password_123');
        $user->generateAuthKey();
        $user->save(false);

        return $user;
    }
}
