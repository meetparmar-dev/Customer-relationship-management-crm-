<?php

namespace console\controllers;

use yii\console\Controller;
use Faker\Factory;
use common\models\Client;
use common\models\ClientAddress;

class ClientAddressController extends Controller
{
    // php yii client-address/seed
    public function actionSeed()
    {
        $faker   = Factory::create('en_IN');
        $clients = Client::find()->all();

        foreach ($clients as $client) {

            // har client ke liye kitne address (1–3)
            $types = $faker->randomElements(
                [
                    ClientAddress::TYPE_OFFICE,
                    ClientAddress::TYPE_BILLING,
                    ClientAddress::TYPE_SHIPPING,
                ],
                $faker->numberBetween(1, 3)
            );

            foreach ($types as $type) {

                $address = new ClientAddress();
                $address->client_id    = $client->id;
                $address->address_type = $type;
                $address->address      = $faker->streetAddress;
                $address->city         = $faker->city;
                $address->state        = $faker->state;
                $address->country      = 'India';
                $address->pincode      = $faker->postcode;

                $address->save(false);
            }
        }

        echo "✅ client addresses (office / billing / shipping) seeded successfully\n";
    }
}
