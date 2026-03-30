<?php

return [
    'admin' => [
        'id' => 1,
        'username' => 'admin',
        'email' => 'admin@test.com',
        'auth_key' => 'testkey',
        'password_hash' => Yii::$app->security->generatePasswordHash('admin123'),
        'status' => 10,
        'created_at' => time(),
        'updated_at' => time(),
    ],
];
