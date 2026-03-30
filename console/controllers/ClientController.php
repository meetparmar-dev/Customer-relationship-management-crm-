<?php

namespace console\controllers;

use yii\console\Controller;
use Faker\Factory;
use common\models\Client;

class ClientController extends Controller
{
    // Run: php yii client/seed 20
    public function actionSeed($limit = 20)
    {
        $faker = Factory::create('en_IN');

        for ($i = 0; $i < $limit; $i++) {

            $client = new Client();

            /* ================= TYPE ================= */
            $type = $faker->randomElement([
                Client::TYPE_INDIVIDUAL,
                Client::TYPE_COMPANY
            ]);

            $client->type = $type;

            /* ================= BASIC ================= */
            $client->email  = $faker->unique()->safeEmail;
            $client->phone  = $faker->phoneNumber;
            $client->status = $faker->randomElement(array_keys(Client::statusList()));

            if ($type === Client::TYPE_COMPANY) {

                /* ================= COMPANY CLIENT ================= */
                $client->company_name = $faker->company;
                $client->first_name   = $faker->firstName;
                $client->last_name    = $faker->lastName;
            } else {

                /* ================= INDIVIDUAL CLIENT ================= */
                $client->first_name   = $faker->firstName;
                $client->last_name    = $faker->lastName;
                $client->company_name = null;
            }

            /* ================= SAVE ================= */
            if (!$client->save(false)) {
                echo "❌ Failed to save client\n";
            }
        }

        echo "✅ {$limit} clients (individual + company) seeded successfully\n";
    }
}
