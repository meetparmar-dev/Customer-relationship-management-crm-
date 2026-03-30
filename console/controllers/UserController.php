<?php

namespace console\controllers;

use yii\console\Controller;
use Faker\Factory;
use common\models\User;

class UserController extends Controller
{
    // php yii user/seed 20
    public function actionSeed($limit = 20)
    {
        $faker = Factory::create('en_IN');

        for ($i = 0; $i < $limit; $i++) {

            $user = new User();

            $user->first_name = $faker->firstName;
            $user->last_name  = $faker->lastName;

            // UNIQUE username & email
            $user->username = strtolower(
                $faker->unique()->userName . rand(100, 999)
            );

            $user->email = $faker->unique()->safeEmail;

            // role (example: 0 = user, 1 = admin)
            $user->role = 0;

            // password
            $password = 'Password@123'; // common fake password
            $user->setPassword($password);

            // auth + verification
            $user->generateAuthKey();
            $user->generateEmailVerificationToken();

            // timestamps (agar columns hain)
            if ($user->hasAttribute('created_at')) {
                $user->created_at = time();
            }
            if ($user->hasAttribute('updated_at')) {
                $user->updated_at = time();
            }

            if (!$user->save(false)) {
                echo "❌ Failed to save user\n";
            }
        }

        echo "✅ {$limit} users seeded successfully\n";
    }
}
