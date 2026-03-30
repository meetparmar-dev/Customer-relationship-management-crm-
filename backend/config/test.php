<?php
return [
    'id' => 'app-backend-tests',

    'components' => [

        //THIS FIXES site/error CRASH
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],

        'urlManager' => [
            'showScriptName' => true,
        ],

        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
        ],
    ],
];
