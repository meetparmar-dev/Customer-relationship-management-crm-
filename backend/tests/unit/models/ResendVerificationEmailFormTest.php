<?php

namespace backend\tests\unit;

use backend\models\ResendVerificationEmailForm;
use common\models\User;
use Yii;
use Codeception\Test\Unit;

class ResendVerificationEmailFormTest extends Unit
{
    protected function _before()
    {
        // clear test emails
        Yii::$app->mailer->fileTransportCallback = function () {
            return 'test_email.eml';
        };
    }

    private function createInactiveUser()
    {
        $user = new User();
        $user->username = 'verify_' . time();
        $user->email = 'verify' . time() . '@test.com';
        $user->setPassword('password123');
        $user->generateAuthKey();
        $user->status = User::STATUS_INACTIVE;
        $user->save(false);

        return $user;
    }

    private function createActiveUser()
    {
        $user = new User();
        $user->username = 'active_' . time();
        $user->email = 'active' . time() . '@test.com';
        $user->setPassword('password123');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->save(false);

        return $user;
    }

    /** ✅ Required + email validation */
    public function testEmailValidation()
    {
        $model = new ResendVerificationEmailForm();
        $model->email = 'not-an-email';

        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('email'));
    }

    /** ❌ User not found */
    public function testEmailNotFound()
    {
        $model = new ResendVerificationEmailForm();
        $model->email = 'nouser@test.com';

        $this->assertFalse($model->validate());
    }

    /** ❌ Active user cannot resend verification */
    public function testActiveUserCannotResend()
    {
        $user = $this->createActiveUser();

        $model = new ResendVerificationEmailForm();
        $model->email = $user->email;

        $this->assertFalse($model->validate());
    }

    /** ✅ Inactive user → email should be sent */
    public function testSendEmailSuccess()
    {
        $user = $this->createInactiveUser();

        $model = new ResendVerificationEmailForm();
        $model->email = $user->email;

        $this->assertTrue($model->validate());
        $this->assertTrue($model->sendEmail());
    }

    /** ❌ sendEmail fails if user not found */
    public function testSendEmailFailsForInvalidUser()
    {
        $model = new ResendVerificationEmailForm();
        $model->email = 'fake@test.com';

        $this->assertFalse($model->sendEmail());
    }
}
