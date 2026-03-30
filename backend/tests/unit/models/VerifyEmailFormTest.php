<?php

namespace backend\tests\unit;

use backend\models\VerifyEmailForm;
use common\models\User;
use yii\base\InvalidArgumentException;
use Codeception\Test\Unit;

class VerifyEmailFormTest extends Unit
{
    protected function createUser($status = User::STATUS_INACTIVE)
    {
        $user = new User();
        $user->username = 'verify_' . time();
        $user->email = 'verify' . time() . '@test.com';
        $user->setPassword('password123');
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        $user->status = $status;
        $user->save(false);

        return $user;
    }

    /** ✅ Token blank ho to exception */
    public function testBlankToken()
    {
        $this->expectException(InvalidArgumentException::class);
        new VerifyEmailForm('');
    }

    /** ✅ Invalid token ho to exception */
    public function testInvalidToken()
    {
        $this->expectException(InvalidArgumentException::class);
        new VerifyEmailForm('wrong-token-123');
    }

    /** ✅ Valid token → email verify ho jaye */
    public function testVerifyEmailSuccess()
    {
        $user = $this->createUser(User::STATUS_INACTIVE);
        $token = $user->verification_token;

        $form = new VerifyEmailForm($token);
        $result = $form->verifyEmail();

        $this->assertInstanceOf(User::class, $result);

        $user->refresh();

        $this->assertEquals(User::STATUS_ACTIVE, $user->status);
        $this->assertNull($user->verification_token);
    }

    /** ✅ Agar user already active hai → null return kare */
    public function testAlreadyVerifiedUser()
    {
        $user = $this->createUser(User::STATUS_INACTIVE);

        // Manually activate the user but keep the token to simulate already verified scenario
        $token = $user->verification_token;
        $user->status = User::STATUS_ACTIVE;
        $user->save(false);

        // Now try to verify with the token - should find the user (since we kept the token)
        // but verifyEmail should return null since user is already active
        $form = new VerifyEmailForm($token);
        $result = $form->verifyEmail();

        $this->assertNull($result);
    }
}
