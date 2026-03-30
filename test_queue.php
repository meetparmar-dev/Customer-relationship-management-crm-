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
    // Find a user to send test email to using raw SQL
    $db = \Yii::$app->db;
    $userData = $db->createCommand('SELECT id, email FROM user WHERE status = 10 LIMIT 1')->queryOne();  // STATUS_ACTIVE is typically 10

    if (!$userData) {
        echo "No active users found to test email with\n";
        exit(1);
    }

    $userId = $userData['id'];
    $userEmail = $userData['email'];

    echo "Using user: $userEmail (ID: $userId)\n";

    // Create a test email job
    $job = new \common\jobs\SendEmailJob([
        'from' => [\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' Test'],
        'to' => $userEmail,
        'subject' => 'Test Email from Queue - ' . date('Y-m-d H:i:s'),
        'htmlBody' => 'welcome-html',
        'textBody' => 'welcome-text',
        'userId' => $userId
    ]);

    $jobId = \Yii::$app->queue->push($job);
    echo "Added test email job to queue with ID: $jobId\n";

    // Check queue status
    $count = $db->createCommand('SELECT COUNT(*) FROM {{%queue}}')->queryScalar();
    echo "Total queue jobs after adding: " . $count . "\n";

    // Process the queue
    echo "Processing queue...\n";
    \Yii::$app->queue->run(false, 0);

    // Check queue status after processing
    $count = $db->createCommand('SELECT COUNT(*) FROM {{%queue}}')->queryScalar();
    echo "Total queue jobs after processing: " . $count . "\n";

    echo "Test completed.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    var_dump($e->getTraceAsString());
}

$app->end();