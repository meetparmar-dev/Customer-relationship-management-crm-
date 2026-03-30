<?php

namespace common\tests\unit\models;

use common\models\User;
use Yii;

class UserTest extends \Codeception\Test\Unit
{
    protected function createUser($data = [])
    {
        $suffix = time() . '_' . rand(1000, 9999); // Add random suffix to avoid conflicts
        $user = new User([
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'username'   => 'john_' . $suffix,
            'email'      => 'john' . $suffix . '@test.com',
            'role'       => User::ROLE_USER,
            'status'     => User::STATUS_ACTIVE,
            'password'   => 'password123',
            'confirm_password' => 'password123'
        ]);

        $user->setScenario('create');

        foreach ($data as $k => $v) {
            $user->$k = $v;
        }

        $user->setPassword($user->password);
        $user->generateAuthKey();

        $this->assertTrue($user->save(), json_encode($user->errors));

        return $user;
    }

    /* ================= VALIDATION ================= */

    public function testRequiredFields()
    {
        $user = new User();
        $user->setScenario('create');
        $this->assertFalse($user->validate());
        $this->assertArrayHasKey('first_name', $user->errors);
        $this->assertArrayHasKey('email', $user->errors);
        $this->assertArrayHasKey('password', $user->errors);
    }

    public function testInvalidEmail()
    {
        $user = new User([
            'first_name' => 'A',
            'last_name' => 'B',
            'username' => 'abc',
            'email' => 'not-an-email',
            'role' => User::ROLE_USER,
            'password' => 'password123',
            'confirm_password' => 'password123',
        ]);
        $user->setScenario('create');

        $this->assertFalse($user->validate());
        $this->assertArrayHasKey('email', $user->errors);
    }

    public function testDuplicateUsername()
    {
        $suffix = time() . '_' . rand(1000, 9999);
        $this->createUser(['username' => 'duplicate_' . $suffix]);

        $user = new User([
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'duplicate_' . $suffix,
            'email' => 'new' . $suffix . '@test.com',
            'role' => User::ROLE_USER,
            'password' => 'password123',
            'confirm_password' => 'password123',
        ]);
        $user->setScenario('create');

        $this->assertFalse($user->validate());
        $this->assertArrayHasKey('username', $user->errors);
    }

    /* ================= PASSWORD ================= */

    public function testPasswordHashingAndValidation()
    {
        $user = $this->createUser();

        $this->assertNotEmpty($user->password_hash);
        $this->assertTrue($user->validatePassword('password123'));
        $this->assertFalse($user->validatePassword('wrongpass'));
    }

    /* ================= AUTH KEY ================= */

    public function testAuthKey()
    {
        $user = $this->createUser();

        $this->assertNotEmpty($user->auth_key);
        $this->assertTrue($user->validateAuthKey($user->auth_key));
        $this->assertFalse($user->validateAuthKey('invalid'));
    }

    /* ================= TOKENS ================= */

    public function testPasswordResetToken()
    {
        $user = $this->createUser();
        $user->generatePasswordResetToken();
        $user->save(false);

        $this->assertNotEmpty($user->password_reset_token);

        $found = User::findByPasswordResetToken($user->password_reset_token);
        $this->assertEquals($user->id, $found->id);

        $this->assertTrue(User::isPasswordResetTokenValid($user->password_reset_token));
    }

    public function testEmailVerificationToken()
    {
        $user = $this->createUser(['status' => User::STATUS_INACTIVE]);
        $user->generateEmailVerificationToken();
        $user->save(false);

        $found = User::findByVerificationToken($user->verification_token);
        $this->assertEquals($user->id, $found->id);
    }

    /* ================= ROLES ================= */

    public function testRoles()
    {
        $user = $this->createUser(['role' => User::ROLE_USER]);
        $admin = $this->createUser(['role' => User::ROLE_ADMIN]);

        $this->assertTrue($user->isUser());
        $this->assertFalse($user->isAdmin());

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isUser());
    }

    /* ================= FULL NAME ================= */

    public function testFullName()
    {
        $user = $this->createUser([
            'first_name' => 'Meet',
            'last_name' => 'Parmar'
        ]);

        $this->assertEquals('Meet Parmar', $user->getFullName());
    }

    /* ================= IDENTITY ================= */

    public function testFindIdentity()
    {
        $user = $this->createUser();

        $found = User::findIdentity($user->id);
        $this->assertEquals($user->id, $found->id);
    }

    public function testInactiveUserCannotLogin()
    {
        $user = $this->createUser(['status' => User::STATUS_INACTIVE]);

        $found = User::findIdentity($user->id);
        $this->assertNull($found);
    }
}
