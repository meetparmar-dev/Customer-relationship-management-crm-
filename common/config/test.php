<?php
return [
    'id' => 'app-common-tests',
    'basePath' => dirname(__DIR__),
    'params' => require __DIR__ . '/params.php',
    'components' => [
        'user' => [
            'class' => \yii\web\User::class,
            'identityClass' => 'common\models\User',
        ],
    ],
];
