<?php

namespace backend\tests\unit\models;

use backend\models\ClientAddressSearch;
use common\models\Client;
use common\models\ClientAddress;

class ClientAddressSearchTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
        ClientAddress::deleteAll();
        Client::deleteAll();
    }

    private function createClient()
    {
        $uid = uniqid();   // always unique

        $client = new Client([
            'client_code' => 'CLT-' . $uid,
            'type' => 'individual',
            'first_name' => 'Meet',
            'last_name' => 'Parmar',
            'email' => $uid . '@test.com',
            'phone' => '9999999999',
            'status' => 1,
        ]);
        $client->save(false);

        return $client;
    }


    private function createAddress($clientId, $city = 'Ahmedabad')
    {
        $address = new ClientAddress([
            'client_id' => $clientId,
            'address_type' => 'Home',
            'address' => 'Test Address',
            'city' => $city,
            'state' => 'Gujarat',
            'country' => 'India',
            'pincode' => '380001',
            'created_at' => time(),
        ]);
        $address->save(false);
        return $address;
    }

    public function testSearchByCity()
    {
        $client = $this->createClient();
        $this->createAddress($client->id, 'Ahmedabad');
        $this->createAddress($client->id, 'Surat');

        $search = new ClientAddressSearch();
        $dataProvider = $search->search([
            'ClientAddressSearch' => [
                'city' => 'Ahmedabad'
            ]
        ]);

        $models = $dataProvider->getModels();

        $this->assertCount(1, $models);
        $this->assertEquals('Ahmedabad', $models[0]->city);
    }

    public function testSearchByClientId()
    {
        $client1 = $this->createClient();
        $client2 = $this->createClient();

        $this->createAddress($client1->id);
        $this->createAddress($client2->id);

        $search = new ClientAddressSearch();
        $dataProvider = $search->search([
            'ClientAddressSearch' => [
                'client_id' => $client1->id
            ]
        ]);

        $models = $dataProvider->getModels();

        $this->assertCount(1, $models);
        $this->assertEquals($client1->id, $models[0]->client_id);
    }
}
