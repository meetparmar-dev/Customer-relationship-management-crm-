<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\models\Client;
use common\models\ClientContact;
use common\models\User;

class ClientContactCest
{
    private function createTestUser()
    {
        // Check if a test admin user already exists to avoid duplicates
        $user = User::findOne(['username' => 'test_admin_contact']);
        if ($user) {
            return $user;
        }

        $user = new User();
        $user->username = 'test_admin_contact';
        $user->email = 'admin_contact@test.com';
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

    protected function createClient()
    {
        $client = new Client();
        $client->client_code = 'CLT-TST';
        $client->type = Client::TYPE_INDIVIDUAL;
        $client->first_name = 'Test';
        $client->last_name = 'Client';
        $client->email = 'client@test.com';
        $client->phone = '9999999999';
        $client->status = 1;
        $client->save(false);

        return $client;
    }

    public function testCreateContact(FunctionalTester $I)
    {
        $client = $this->createClient();

        $I->amOnPage('/client-contact/create?client_id=' . $client->id);

        $I->submitForm('form', [
            'ClientContact[name]'  => 'Meet Parmar',
            'ClientContact[email]' => 'meet@test.com',
            'ClientContact[phone]' => '9876543210',
            'ClientContact[designation]' => 'Manager',
            'ClientContact[client_id]' => $client->id,
        ]);

        $I->see('Contact added successfully');

        $I->seeRecord(ClientContact::class, [
            'email' => 'meet@test.com',
            'client_id' => $client->id
        ]);
    }

    public function testUpdateContact(FunctionalTester $I)
    {
        $client = $this->createClient();

        $contact = new ClientContact();
        $contact->client_id = $client->id;
        $contact->name = 'Old Name';
        $contact->email = 'old@test.com';
        $contact->phone = '1111111111';
        $contact->save(false);

        $I->amOnPage('/client-contact/update?id=' . $contact->id);

        $I->submitForm('form', [
            'ClientContact[name]'  => 'Updated Name',
            'ClientContact[email]' => 'updated@test.com',
        ]);

        $I->see('Client updated successfully');

        $I->seeRecord(ClientContact::class, [
            'id' => $contact->id,
            'email' => 'updated@test.com'
        ]);
    }

    public function testViewContact(FunctionalTester $I)
    {
        $client = $this->createClient();

        $contact = new ClientContact();
        $contact->client_id = $client->id;
        $contact->name = 'View Person';
        $contact->email = 'view@test.com';
        $contact->phone = '2222222222';
        $contact->save(false);

        $I->amOnPage('/client-contact/view?id=' . $contact->id);

        $I->see('View Person');
        $I->see('view@test.com');
    }

    public function testDeleteContact(FunctionalTester $I)
    {
        $client = $this->createClient();

        $contact = new ClientContact();
        $contact->client_id = $client->id;
        $contact->name = 'Delete Me';
        $contact->email = 'delete@test.com';
        $contact->phone = '3333333333';
        $contact->save(false);

        // VerbFilter requires POST
        $I->sendAjaxPostRequest('/client-contact/delete?id=' . $contact->id);

        $I->dontSeeRecord(ClientContact::class, [
            'id' => $contact->id
        ]);
    }


    /* ===============================
       GRID & SEARCH TESTS
       For ClientContactSearch
    =============================== */

    public function testIndexPageLoads(FunctionalTester $I)
    {
        $I->amOnPage('/client-contact/index');
        $I->seeResponseCodeIs(200);
    }

    public function testSearchByName(FunctionalTester $I)
    {
        $client = $this->createClient();

        $contact = new ClientContact();
        $contact->client_id = $client->id;
        $contact->name = 'Rohit Sharma';
        $contact->email = 'rohit@test.com';
        $contact->phone = '9999999999';
        $contact->is_primary = 1;
        $contact->created_at = time();
        $contact->save(false);

        $I->amOnPage('/client-contact/index?ClientContactSearch[name]=Rohit');
        $I->see('Rohit Sharma');
    }

    public function testSearchByEmail(FunctionalTester $I)
    {
        $client = $this->createClient();

        $contact = new ClientContact();
        $contact->client_id = $client->id;
        $contact->name = 'Email Test';
        $contact->email = 'emailfilter@test.com';
        $contact->phone = '8888888888';
        $contact->save(false);

        $I->amOnPage('/client-contact/index?ClientContactSearch[email]=emailfilter@test.com');
        $I->see('emailfilter@test.com');
    }

    public function testSearchByClientId(FunctionalTester $I)
    {
        $client = $this->createClient();

        $contact = new ClientContact();
        $contact->client_id = $client->id;
        $contact->name = 'Client Filter';
        $contact->email = 'clientfilter@test.com';
        $contact->phone = '7777777777';
        $contact->save(false);

        $I->amOnPage('/client-contact/index?ClientContactSearch[client_id]=' . $client->id);
        $I->see('Client Filter');
    }

    public function testSearchByIsPrimary(FunctionalTester $I)
    {
        $client = $this->createClient();

        $contact = new ClientContact();
        $contact->client_id = $client->id;
        $contact->name = 'Primary Contact';
        $contact->email = 'primary@test.com';
        $contact->phone = '6666666666';
        $contact->is_primary = 1;
        $contact->save(false);

        $I->amOnPage('/client-contact/index?ClientContactSearch[is_primary]=1');
        $I->see('Primary Contact');
    }
}
