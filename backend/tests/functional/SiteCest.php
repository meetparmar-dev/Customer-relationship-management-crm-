<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\models\User;

class SiteCest
{
    public function _before(FunctionalTester $I)
    {
        // Remove old test users
        User::deleteAll(['username' => 'testadmin']);
        User::deleteAll(['username' => 'testuser']);

        // Create ADMIN
        $admin = new User();
        $admin->scenario = 'create';
        $admin->username = 'testadmin';
        $admin->email = 'testadmin@test.com';
        $admin->first_name = 'Test';
        $admin->last_name = 'Admin';
        $admin->role = User::ROLE_ADMIN;
        $admin->status = User::STATUS_ACTIVE;
        $admin->password = 'admin123';
        $admin->confirm_password = 'admin123';
        $admin->generateAuthKey();
        $admin->setPassword('admin123');
        $admin->save(false);

        // Create NORMAL USER
        $user = new User();
        $user->scenario = 'create';
        $user->username = 'testuser';
        $user->email = 'testuser@test.com';
        $user->first_name = 'Test';
        $user->last_name = 'User';
        $user->role = User::ROLE_USER;
        $user->status = User::STATUS_ACTIVE;
        $user->password = 'user123';
        $user->confirm_password = 'user123';
        $user->generateAuthKey();
        $user->setPassword('user123');
        $user->save(false);

        // logout if already logged in
        $I->amOnPage('/site/logout');
    }


    /* ================= LOGIN ================= */

    public function testAdminLogin(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');

        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'testadmin',
            'LoginForm[password]' => 'admin123',
        ]);

        $I->see('Dashboard');
    }


    public function testInvalidLogin(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');

        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'wrong',
            'LoginForm[password]' => 'wrong',
        ]);

        $I->see('Incorrect username or password');
    }

    /* ================= ACCESS CONTROL ================= */

    public function testGuestCannotAccessDashboard(FunctionalTester $I)
    {
        $I->amOnPage('/site/index');

        $I->seeInCurrentUrl('/site/login');
    }

    public function testNonAdminCannotAccessDashboard(FunctionalTester $I)
    {
        $user = User::findOne(['username' => 'testuser']);
        $I->amLoggedInAs($user->id);

        $I->amOnPage('/site/index');

        // Yii shows a 403 forbidden page
        $I->seeResponseCodeIs(403);
        $I->see('Forbidden');
    }




    public function testAdminCanAccessDashboard(FunctionalTester $I)
    {
        $admin = User::findOne(['username' => 'testadmin']);
        $I->amLoggedInAs($admin->id);

        $I->amOnPage('/site/index');

        $I->see('Projects');
        $I->see('Clients');
    }


    /* ================= DASHBOARD API ================= */

    public function testDashboardStatsApi(FunctionalTester $I)
    {
        $admin = User::findOne(['role' => User::ROLE_ADMIN]);
        $I->amLoggedInAs($admin->id);

        $I->amOnPage('/site/get-dashboard-stats?period=Last 3 Months');

        $I->seeResponseCodeIs(200);
        $I->see('clients');
        $I->see('projects');
        $I->see('tasks');
    }


    /* ================= LOGOUT ================= */

    public function testLogout(FunctionalTester $I)
    {
        $admin = User::findOne(['username' => 'testadmin']);
        $I->amLoggedInAs($admin->id);

        // Logout via POST
        $I->sendAjaxPostRequest('/site/logout');

        // Try to access dashboard as guest
        $I->amOnPage('/site/index');

        // Guest should see login page
        $I->see('Login');
    }
}
