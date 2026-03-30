<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the api
            'name' => 'advanced-api',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,

            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => array_combine(
                        array_map(
                            fn($k) => "v1/$k",
                            array_keys(require __DIR__ . '/routes/v1.php')
                        ),
                        array_map(
                            fn($v) => "v1/$v",
                            require __DIR__ . '/routes/v1.php'
                        )
                    ),
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST status' => 'status',
                    ],
                ],
            ],
        ],

        'request' => [
            'csrfParam' => '_csrf-api',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
    ],
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module'
        ]
    ],
    'timeZone' => 'Asia/Kolkata',
    'params' => $params,
];
