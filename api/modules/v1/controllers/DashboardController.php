<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;
use common\models\Client;
use common\models\Project;
use common\models\Task;

class DashboardController extends Controller
{
    public function behaviors()
    {
        return [
            // 🔐 JWT Authentication
            'authenticator' => [
                'class' => HttpBearerAuth::class,
            ],

            // 🔒 HTTP Verb rules
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'stats' => ['GET'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->headers->set('Content-Type', 'application/json');
        return parent::beforeAction($action);
    }

    /**
     * GET /v1/dashboard
     */
    public function actionIndex()
    {
        $clientCount  = Client::find()->count();
        $projectCount = Project::find()->count();
        $taskCount    = Task::find()->count();

        $taskStatus = Task::find()
            ->select(['status', 'COUNT(*) AS total'])
            ->groupBy('status')
            ->indexBy('status')
            ->asArray()
            ->all();

        return [
            'success' => true,
            'data' => [
                'clients'   => (int) $clientCount,
                'projects'  => (int) $projectCount,
                'tasks'     => (int) $taskCount,
                'pending'   => (int) ($taskStatus['pending']['total'] ?? 0),
                'completed' => (int) ($taskStatus['completed']['total'] ?? 0),
            ],
        ];
    }

    /**
     * GET /v1/dashboard/stats?period=Last 6 Months
     */
    public function actionStats($period = 'Last 6 Months')
    {
        $months = match ($period) {
            'Last 1 Month'   => 1,
            'Last 3 Months'  => 3,
            'Last 6 Months'  => 6,
            'Last 12 Months' => 12,
            default => 6,
        };

        $fromTs = strtotime("-{$months} months");

        $clients  = Client::find()->where(['>=', 'created_at', $fromTs])->count();
        $projects = Project::find()->where(['>=', 'created_at', $fromTs])->count();
        $tasks    = Task::find()->where(['>=', 'created_at', $fromTs])->count();

        return [
            'success' => true,
            'data' => [
                'period'   => $period,
                'clients'  => (int) $clients,
                'projects' => (int) $projects,
                'tasks'    => (int) $tasks,
            ],
        ];
    }
}
