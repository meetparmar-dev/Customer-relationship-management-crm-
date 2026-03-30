<?php

namespace tests\unit\models;

use common\models\Client;
use common\models\ClientAddress;

class ClientAddressTest extends \Codeception\Test\Unit
{
    protected function createClient()
    {
        $client = new Client();
        $client->client_code = 'CLT-' . time();
        $client->type = Client::TYPE_INDIVIDUAL;
        $client->first_name = 'John';
        $client->email = 'john' . time() . '@test.com';
        $client->phone = '9999999999';
        $client->save(false);

        return $client;
    }

    private function getValidData($clientId)
    {
        return [
            'client_id' => $clientId,
            'address_type' => ClientAddress::TYPE_BILLING,
            'address' => 'Street 1, Test Area',
            'city' => 'Ahmedabad',
            'state' => 'Gujarat',
            'pincode' => '380001',
        ];
    }

    /** ✅ Required fields */
    public function testRequiredFields()
    {
        $model = new ClientAddress();
        $this->assertFalse($model->validate());

        $this->assertArrayHasKey('client_id', $model->errors);
        $this->assertArrayHasKey('address_type', $model->errors);
        $this->assertArrayHasKey('address', $model->errors);
    }

    /** ✅ Valid address saves */
    public function testValidAddressSaves()
    {
        $client = $this->createClient();

        $model = new ClientAddress($this->getValidData($client->id));
        $this->assertTrue($model->save());

        $this->assertNotEmpty($model->id);
    }

    /** ❌ Invalid address type */
    public function testInvalidAddressType()
    {
        $client = $this->createClient();

        $data = $this->getValidData($client->id);
        $data['address_type'] = 'home';

        $model = new ClientAddress($data);
        $this->assertFalse($model->validate());
        $this->assertArrayHasKey('address_type', $model->errors);
    }

    /** ❌ Duplicate address type for same client */
    public function testDuplicateAddressType()
    {
        $client = $this->createClient();

        $first = new ClientAddress($this->getValidData($client->id));
        $this->assertTrue($first->save());

        $second = new ClientAddress($this->getValidData($client->id));
        $this->assertFalse($second->validate());
        $this->assertArrayHasKey('address_type', $second->errors);
    }

    /** 🔗 Client relation */
    public function testClientRelation()
    {
        $client = $this->createClient();

        $address = new ClientAddress($this->getValidData($client->id));
        $address->save();

        $this->assertEquals($client->id, $address->client->id);
    }
}
