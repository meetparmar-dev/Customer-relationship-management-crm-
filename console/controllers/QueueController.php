<?php

namespace console\controllers;

use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Queue management commands
 */
class QueueController extends Controller
{
    /**
     * Runs the queue worker to process jobs
     */
    public function actionListen()
    {
        echo "Starting queue listener...\n";
        \Yii::$app->queue->run(false, 0);  // Process available jobs without waiting for new ones
    }

    /**
     * Runs a single job from the queue
     */
    public function actionRun()
    {
        // For DB queue, we need to use reserve and release methods
        $reserveTime = 300; // 5 minutes
        $job = \Yii::$app->queue->reserve($reserveTime);

        if ($job) {
            echo "Processing job...\n";
            try {
                $job->execute(\Yii::$app->queue);
                \Yii::$app->queue->release($job, 0, 0); // Success - remove from queue
                echo "Job completed.\n";
            } catch (\Exception $e) {
                \Yii::$app->queue->release($job, 0, 1); // Error - increment attempt count
                echo "Job failed: " . $e->getMessage() . "\n";
            }
        } else {
            echo "No jobs in queue.\n";
        }
    }
}
