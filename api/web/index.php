<?php

// Enable debug
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

// STEP 1 — Load Composer
require __DIR__ . '/../../vendor/autoload.php';

// STEP 2 — Load Yii framework
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';

// STEP 3 — Load bootstrap files
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../config/bootstrap.php';

// STEP 4 — Load config
$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../config/main.php',
    require __DIR__ . '/../config/main-local.php'
);

// RUN APP
(new yii\web\Application($config))->run();
