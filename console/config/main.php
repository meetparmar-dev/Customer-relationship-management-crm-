<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',

    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],

    'controllerMap' => [
        'fixture' => [
            'class' => \yii\console\controllers\FixtureController::class,
            'namespace' => 'common\fixtures',
        ],
    ],

    // 🔥 IMPORTANT PART
    'components' => [

        // ✅ DB auto-load hoga from common/config/main-local.php
        // ❌ Yahan db require karne ki zarurat nahi

        // 🔥 REQUIRED for email URLs in queue
        'urlManager' => [
            'class' => yii\web\UrlManager::class,
            'enablePrettyUrl' => true,
            'showScriptName' => false,

            // CHANGE THIS FOR PRODUCTION
            'hostInfo' => 'http://localhost',
            'baseUrl' => '',
        ],

        'log' => [
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning', 'info'],
                ],
            ],
        ],
    ],

    'params' => $params,
];
