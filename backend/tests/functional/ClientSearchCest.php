<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\models\Client;
use PHPUnit\Framework\Assert;

class ClientSearchCest
{
    public function _before(FunctionalTester $I)
    {
        // Clean up any existing test admin user
        $user = \common\models\User::findOne(['username' => 'test_admin_client_search']);
        if ($user) {
            $user->delete();
        }

        // Create test admin user
        $user = new \common\models\User();
        $user->username = 'test_admin_client_search';
        $user->email = 'admin_client_search@test.com';
        $user->first_name = 'Test';
        $user->last_name = 'Admin';
        $user->role = \common\models\User::ROLE_ADMIN;
        $user->status = \common\models\User::STATUS_ACTIVE;
        $user->password = 'password123';
        $user->generateAuthKey();
        $user->setPassword('password123');
        $user->save(false);

        // Login as admin
        $I->amLoggedInAs($user->id);

        // Clean table
        Client::deleteAll();

        // Create test clients
        $client1 = new Client();
        $client1->client_code = 'CLT001';
        $client1->type = 'individual';
        $client1->first_name = 'Meet';
        $client1->last_name = 'Parmar';
        $client1->email = 'meet@test.com';
        $client1->phone = '1111111111';
        $client1->status = 1;
        $client1->save(false);

        $client2 = new Client();
        $client2->client_code = 'CLT002';
        $client2->type = 'company';
        $client2->company_name = 'GenZ Pvt Ltd';
        $client2->first_name = 'Ravi';
        $client2->last_name = 'Shah';
        $client2->email = 'ravi@test.com';
        $client2->phone = '2222222222';
        $client2->status = 1;
        $client2->save(false);

        $client3 = new Client();
        $client3->client_code = 'CLT003';
        $client3->type = 'individual';
        $client3->first_name = 'Amit';
        $client3->last_name = 'Patel';
        $client3->email = 'amit@test.com';
        $client3->phone = '3333333333';
        $client3->status = 0;
        $client3->save(false);
    }

    /**
     * Full name search test
     */
    public function testFullNameSearch(FunctionalTester $I)
    {
        $I->amOnPage('/client/index?ClientSearch[full_name]=Meet');

        $I->see('Meet');
        $I->see('Parmar');

        // Should not see other users
        $I->dontSee('Amit');
        $I->dontSee('Ravi');
    }

    /**
     * Full name combined search
     */
    public function testFullNameCombinedSearch(FunctionalTester $I)
    {
        $I->amOnPage('/client/index?ClientSearch[full_name]=Meet Parmar');

        $I->see('Meet');
        $I->see('Parmar');
        $I->dontSee('Amit');
    }

    /**
     * Status filter test
     */
    public function testStatusFilter(FunctionalTester $I)
    {
        $I->amOnPage('/client/index?ClientSearch[status]=0');

        $I->see('Amit');
        $I->dontSee('Meet');
        $I->dontSee('Ravi');
    }

    /**
     * Type filter test
     */
    public function testTypeFilter(FunctionalTester $I)
    {
        $I->amOnPage('/client/index?ClientSearch[type]=company');

        $I->see('GenZ Pvt Ltd');
        $I->dontSee('Meet');
        $I->dontSee('Amit');
    }

    /**
     * Sorting by full_name ASC
     */
    public function testFullNameSortingAsc(FunctionalTester $I)
    {
        $I->amOnPage('/client/index?sort=full_name');

        $page = $I->grabTextFrom('table');

        // Alphabetically: Amit, Meet, Ravi
        Assert::assertTrue(
            strpos($page, 'Amit') < strpos($page, 'Meet')
                && strpos($page, 'Meet') < strpos($page, 'Ravi')
        );
    }

    /**
     * Sorting by full_name DESC
     */
    public function testFullNameSortingDesc(FunctionalTester $I)
    {
        $I->amOnPage('/client/index?sort=-full_name');

        $page = $I->grabTextFrom('table');

        // Reverse: Ravi, Meet, Amit
        Assert::assertTrue(
            strpos($page, 'Ravi') < strpos($page, 'Meet')
                && strpos($page, 'Meet') < strpos($page, 'Amit')
        );
    }

    public function _after(FunctionalTester $I)
    {
        // Clean up test admin user
        $user = \common\models\User::findOne(['username' => 'test_admin_client_search']);
        if ($user) {
            $user->delete();
        }
    }
}
