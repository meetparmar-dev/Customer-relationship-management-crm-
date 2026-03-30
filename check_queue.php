<?php
require_once 'vendor/autoload.php';
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require_once 'vendor/yiisoft/yii2/Yii.php';

// Load console config which includes the app id
$config = require_once 'console/config/main.php';
$localConfig = require_once 'common/config/main-local.php';
$config = yii\helpers\ArrayHelper::merge($config, $localConfig);

$app = new yii\console\Application($config);

try {
    $db = Yii::$app->db;
    
    // Check if queue table exists
    $tableExists = $db->schema->getTableSchema('{{%queue}}');
    if (!$tableExists) {
        echo "Queue table does not exist!\n";
        exit(1);
    }
    
    $count = $db->createCommand('SELECT COUNT(*) FROM {{%queue}}')->queryScalar();
    echo "Total queue jobs: " . $count . "\n";
    
    $pending = $db->createCommand('SELECT COUNT(*) FROM {{%queue}} WHERE reserved_at IS NULL AND done_at IS NULL')->queryScalar();
    echo "Pending jobs: " . $pending . "\n";
    
    $reserved = $db->createCommand('SELECT COUNT(*) FROM {{%queue}} WHERE reserved_at IS NOT NULL AND done_at IS NULL')->queryScalar();
    echo "Reserved jobs: " . $reserved . "\n";
    
    $finished = $db->createCommand('SELECT COUNT(*) FROM {{%queue}} WHERE done_at IS NOT NULL')->queryScalar();
    echo "Finished jobs: " . $finished . "\n";
    
    if ($pending > 0) {
        echo "\nDetails of pending jobs:\n";
        $jobs = $db->createCommand('SELECT id, job, pushed_at, attempt FROM {{%queue}} WHERE reserved_at IS NULL AND done_at IS NULL LIMIT 5')->queryAll();
        foreach ($jobs as $job) {
            echo "- ID: {$job['id']}, Pushed: " . date('Y-m-d H:i:s', $job['pushed_at']) . ", Attempts: {$job['attempt']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$app->end();