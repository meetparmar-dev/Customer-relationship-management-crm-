<?php

namespace console\controllers;

use yii\console\Controller;
use Faker\Factory;
use common\models\Client;
use common\models\ClientContact;

class ClientContactController extends Controller
{
    // php yii client-contact/seed 2
    // 2 = contacts per COMPANY client
    public function actionSeed($perClient = 2)
    {
        $faker   = Factory::create('en_IN');
        $clients = Client::find()->all();

        if (empty($clients)) {
            echo "❌ No clients found. Seed clients first.\n";
            return;
        }

        foreach ($clients as $client) {

            // 🔥 company = multiple, individual = only 1
            $contactsToCreate = ($client->type === Client::TYPE_COMPANY)
                ? $perClient
                : 1;

            for ($i = 0; $i < $contactsToCreate; $i++) {

                $contact = new ClientContact();

                /* ================= REQUIRED ================= */
                $contact->client_id = $client->id;
                $contact->name      = $faker->name;

                /* ================= OPTIONAL ================= */
                $contact->designation = $faker->randomElement([
                    'Manager',
                    'Owner',
                    'Director',
                    'HR',
                    'Accountant',
                    'Sales Executive',
                ]);

                $contact->email = $faker->unique()->safeEmail;
                $contact->phone = $faker->phoneNumber;

                /* ================= PRIMARY CONTACT ================= */
                // ✔ individual → always primary
                // ✔ company → first primary, rest secondary
                if ($contact->hasAttribute('is_primary')) {
                    $contact->is_primary = ($i === 0) ? 1 : 0;
                }

                /* ================= TIMESTAMP (OPTIONAL) ================= */
                if ($contact->hasAttribute('created_at')) {
                    $contact->created_at = date('Y-m-d H:i:s');
                }

                $contact->save(false);
            }
        }

        echo "✅ client contacts seeded (company = multiple, individual = single primary)\n";
    }
}
