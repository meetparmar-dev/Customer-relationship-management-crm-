<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\models\Client;
use common\models\ClientAddress;
use common\models\User;
use Yii;

class ClientAddressCest
{
    private $clientId;
    private $addressId;

    public function _before(FunctionalTester $I)
    {
        /** -------------------------
         * Create Admin User
         * ------------------------- */
        $user = new User();
        $user->username = 'admin_' . time();
        $user->email = 'admin' . time() . '@test.com';
        $user->setPassword('password123');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->save(false);

        $I->amLoggedInAs($user->id);

        /** -------------------------
         * Create Client
         * ------------------------- */
        $client = new Client();

        // USE REAL CLIENT FIELDS (adjust ONLY if names differ)
        $client->first_name = 'Test';
        $client->last_name  = 'Client';
        $client->email      = 'client@test.com';
        $client->phone      = '9999999999';
        $client->status     = 1;
        $client->save(false);

        $this->clientId = $client->id;

        /** -------------------------
         * Create Client Address
         * ------------------------- */
        $address = new ClientAddress();
        $address->client_id    = $this->clientId;
        $address->address_type = ClientAddress::TYPE_BILLING;
        $address->address      = 'Test Address';
        $address->city         = 'Ahmedabad';
        $address->state        = 'Gujarat';
        $address->pincode      = '380001';
        $address->save(false);

        $this->addressId = $address->id;
    }

    /** ✅ INDEX */
    public function testIndex(FunctionalTester $I)
    {
        $I->amOnRoute('client-address/index');
        $I->seeResponseCodeIs(200);
        $I->see('Client Addresses');
    }

    /** ✅ CREATE WITHOUT CLIENT ID */
    public function testCreateWithoutClientId(FunctionalTester $I)
    {
        $I->amOnRoute('client-address/create');
        $I->seeResponseCodeIs(200);
        $I->see('Client');
    }

    /** ✅ CREATE ADDRESS */
    public function testCreateAddress(FunctionalTester $I)
    {
        $I->amOnRoute('client-address/create', [
            'client_id' => $this->clientId
        ]);

        $I->submitForm('form', [
            'ClientAddress[address_type]' => ClientAddress::TYPE_SHIPPING,
            'ClientAddress[address]'      => 'New Shipping Address',
            'ClientAddress[city]'         => 'Surat',
            'ClientAddress[state]'        => 'Gujarat',
            'ClientAddress[pincode]'      => '395001',
        ]);

        // redirected to client/view
        $I->seeInCurrentUrl('client/view');

        $I->seeRecord(ClientAddress::class, [
            'client_id'    => $this->clientId,
            'address_type' => ClientAddress::TYPE_SHIPPING,
            'city'         => 'Surat',
        ]);
    }



    /** ✅ DELETE ADDRESS */
    public function testDeleteAddress(FunctionalTester $I)
    {
        $csrf = Yii::$app->request->getCsrfToken();

        // Mark request as AJAX
        $I->haveHttpHeader('X-Requested-With', 'XMLHttpRequest');

        // Send CSRF in header
        $I->haveHttpHeader('X-CSRF-Token', $csrf);

        // Send CSRF in POST body
        $I->sendAjaxPostRequest(
            '/client-address/delete?id=' . $this->addressId,
            [
                Yii::$app->request->csrfParam => $csrf,
            ]
        );

        // Now it MUST be deleted
        $I->dontSeeRecord(ClientAddress::class, [
            'id' => $this->addressId
        ]);
    }

    public function testUpdateAddress(FunctionalTester $I)
    {
        $I->amOnRoute('client-address/update', [
            'id' => $this->addressId
        ]);

        $I->submitForm('form', [
            'ClientAddress[city]' => 'Vadodara'
        ]);

        $I->seeInCurrentUrl('client/view');

        $I->seeRecord(ClientAddress::class, [
            'id'   => $this->addressId,
            'city' => 'Vadodara'
        ]);
    }
}