<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\models\User;

class UserCest
{
    private function createTestUser()
    {
        // Check if a test admin user already exists to avoid duplicates
        $user = User::findOne(['username' => 'test_admin_user']);
        if ($user) {
            return $user;
        }

        $user = new User();
        $user->username = 'test_admin_user';
        $user->email = 'admin_user@test.com';
        $user->first_name = 'Test';
        $user->last_name = 'Admin';
        $user->status = User::STATUS_ACTIVE;
        $user->role = User::ROLE_ADMIN;
        $user->setPassword('password123');
        $user->generateAuthKey();
        $user->save(false);

        return $user;
    }

    public function _before(FunctionalTester $I)
    {
        $user = $this->createTestUser();
        $I->amLoggedInAs($user->id);
    }

    public function testUserIndex(FunctionalTester $I)
    {
        $I->amOnPage('/user/index');
        $I->see('Users');
    }

    public function testCreateUser(FunctionalTester $I)
    {
        $email = 'test' . time() . '@test.com';
        $username = 'test' . time();

        $I->amOnPage('/user/create');

        $I->submitForm('form', [
            'User[first_name]'        => 'Test',
            'User[last_name]'         => 'User',
            'User[username]'          => $username,
            'User[email]'             => $email,
            'User[password]'          => '123456',
            'User[confirm_password]'  => '123456',
            'User[role]'              => User::ROLE_USER,
            'User[status]'            => 10,
        ]);

        $I->seeRecord(User::class, [
            'email' => $email
        ]);
    }

    public function testViewUser(FunctionalTester $I)
    {
        $user = User::find()->orderBy(['id' => SORT_DESC])->one();

        $I->amOnPage('/user/view?id=' . $user->id);
        $I->see($user->email);
    }

    public function testUpdateUser(FunctionalTester $I)
    {
        $user = new User();
        $user->first_name = 'Old';
        $user->last_name  = 'Name';
        $user->username   = 'update_' . time();
        $user->email      = 'update_' . time() . '@test.com';
        $user->setPassword('123456');
        $user->generateAuthKey();
        $user->status = 10;
        $user->role = User::ROLE_USER;
        $user->save(false);

        $I->amOnPage('/user/update?id=' . $user->id);

        $I->submitForm('form', [
            'User[first_name]' => 'Updated',
        ]);

        $I->seeRecord(User::class, [
            'id' => $user->id,
            'first_name' => 'Updated'
        ]);
    }

    public function testDeleteUser(FunctionalTester $I)
    {
        $user = new User();
        $user->first_name = 'Delete';
        $user->last_name  = 'Me';
        $user->username   = 'delete_' . time();
        $user->email      = 'delete_' . time() . '@test.com';
        $user->setPassword('123456');
        $user->generateAuthKey();
        $user->status = 10;
        $user->role = User::ROLE_USER;
        $user->save(false);

        $I->sendAjaxPostRequest('/user/delete?id=' . $user->id);

        $I->dontSeeRecord(User::class, [
            'id' => $user->id
        ]);
    }
}
