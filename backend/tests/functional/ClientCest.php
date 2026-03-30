<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\models\Client;
use common\models\User;

class ClientCest
{
    private function createTestUser()
    {
        // Check if a test admin user already exists to avoid duplicates
        $user = User::findOne(['username' => 'test_admin_client']);
        if ($user) {
            return $user;
        }

        $user = new User();
        $user->username = 'test_admin_client';
        $user->email = 'admin_client@test.com';
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

    private function createClient()
    {
        $client = new Client();
        $client->client_code = 'TST' . rand(1000, 9999);
        $client->type = Client::TYPE_INDIVIDUAL;
        $client->first_name = 'Test';
        $client->last_name = 'User';
        $client->email = 'test' . rand(1000, 9999) . '@test.com';
        $client->phone = '9999999999';
        $client->status = Client::STATUS_ACTIVE;
        $client->save(false);

        return $client;
    }

    /* ================= CREATE ================= */
    public function testCreateClient(FunctionalTester $I)
    {
        $I->amOnPage('/client/create');

        $I->submitForm('form', [
            'Client[client_code]' => 'CLT100',
            'Client[type]' => Client::TYPE_INDIVIDUAL,
            'Client[first_name]' => 'Meet',
            'Client[last_name]' => 'Parmar',
            'Client[email]' => 'meet@test.com',
            'Client[phone]' => '9999999999',
            'Client[status]' => Client::STATUS_ACTIVE,
        ]);

        $I->see('Client created successfully');

        $I->seeRecord(Client::class, [
            'email' => 'meet@test.com',
            'status' => Client::STATUS_ACTIVE
        ]);
    }

    /* ================= INDEX ================= */
    public function testIndexPage(FunctionalTester $I)
    {
        $I->amOnPage('/client/index');
        $I->see('Clients');
        $I->seeElement('table');
    }

    /* ================= VIEW ================= */
    public function testViewClient(FunctionalTester $I)
    {
        $client = $this->createClient();

        $I->amOnPage('/client/view?id=' . $client->id);
        $I->see($client->first_name);
        $I->see($client->email);
    }

    /* ================= UPDATE ================= */
    public function testUpdateClient(FunctionalTester $I)
    {
        $client = $this->createClient();

        $I->amOnPage('/client/update?id=' . $client->id);

        $I->submitForm('form', [
            'Client[first_name]' => 'Updated',
            'Client[last_name]' => 'Name',
        ]);

        $I->see('Record updated successfully.');

        $I->seeRecord(Client::class, [
            'id' => $client->id,
            'first_name' => 'Updated'
        ]);
    }

    /* ================= CHANGE STATUS (AJAX) ================= */
    public function testChangeStatus(FunctionalTester $I)
    {
        $client = $this->createClient();

        // Call AJAX endpoint
        $I->sendAjaxPostRequest('/client/change-status', [
            'id' => $client->id,
            'status' => Client::STATUS_INACTIVE,
        ]);

        $I->seeRecord(Client::class, [
            'id' => $client->id,
            'status' => Client::STATUS_INACTIVE
        ]);
    }


    /* ================= DELETE ================= */
    public function testDeleteClient(FunctionalTester $I)
    {
        $client = $this->createClient();

        $I->sendAjaxPostRequest('/client/delete?id=' . $client->id);

        $I->dontSeeRecord(Client::class, [
            'id' => $client->id
        ]);
    }
}
