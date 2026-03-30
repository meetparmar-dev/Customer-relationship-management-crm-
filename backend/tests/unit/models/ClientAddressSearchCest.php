<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\models\Client;
use common\models\ClientAddress;
use PHPUnit\Framework\Assert;

class ClientAddressSearchCest
{
    private function createClient()
    {
        $uid = uniqid();

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

    private function createAddress($clientId, $city)
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

    public function searchByCityWorks(FunctionalTester $I)
    {
        $client = $this->createClient();

        $this->createAddress($client->id, 'Ahmedabad');
        $this->createAddress($client->id, 'Surat');

        $search = new \backend\models\ClientAddressSearch();
        $dataProvider = $search->search([
            'ClientAddressSearch' => [
                'city' => 'Ahmedabad'
            ]
        ]);

        $models = $dataProvider->getModels();

        Assert::assertCount(1, $models);
        Assert::assertEquals('Ahmedabad', $models[0]->city);
    }

    public function searchByClientIdWorks(FunctionalTester $I)
    {
        $client1 = $this->createClient();
        $client2 = $this->createClient();

        $this->createAddress($client1->id, 'Rajkot');
        $this->createAddress($client2->id, 'Surat');

        $search = new \backend\models\ClientAddressSearch();
        $dataProvider = $search->search([
            'ClientAddressSearch' => [
                'client_id' => $client1->id
            ]
        ]);

        $models = $dataProvider->getModels();

        Assert::assertCount(1, $models);
        Assert::assertEquals($client1->id, $models[0]->client_id);
    }
}
