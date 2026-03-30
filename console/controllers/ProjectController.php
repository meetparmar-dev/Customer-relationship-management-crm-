<?php

namespace console\controllers;

use yii\console\Controller;
use Faker\Factory;
use common\models\Client;
use common\models\Project;
use common\models\User;

class ProjectController extends Controller
{
    // php yii project/seed 30
    public function actionSeed($limit = 30)
    {
        $faker   = Factory::create('en_IN');
        $clients = Client::find()->limit($limit)->all();

        // 🔥 all user ids (for project manager)
        $userIds = User::find()
            ->select('id')
            ->column();

        foreach ($clients as $client) {

            $project = new Project();

            /* ================= BASIC ================= */
            $project->client_id    = $client->id;
            $project->project_name = $faker->sentence(3);
            $project->description  = $faker->paragraph;

            /* ================= FORCE OLD MONTHS (LAST 6 MONTHS) ================= */
            $createdAt = $faker
                ->dateTimeBetween('-6 months', '-5 days')
                ->getTimestamp();

            $project->created_at = $createdAt;

            /* ================= STATUS BASED ON AGE ================= */
            if ($createdAt < strtotime('-4 months')) {
                $project->status = Project::STATUS_COMPLETED;
            } elseif ($createdAt < strtotime('-2 months')) {
                $project->status = Project::STATUS_ACTIVE;
            } else {
                $project->status = Project::STATUS_PLANNED;
            }

            /* ================= PRIORITY & BILLING ================= */
            $project->priority     = $faker->randomElement(array_keys(Project::priorityList()));
            $project->billing_type = $faker->randomElement(array_keys(Project::billingTypeList()));

            /* ================= NUMBERS ================= */
            $project->budget          = $faker->numberBetween(50000, 500000);
            $project->estimated_hours = $faker->numberBetween(20, 300);

            /* ================= DATES ================= */
            $project->start_date = date('Y-m-d', $createdAt);

            if ($project->status === Project::STATUS_COMPLETED) {
                $project->end_date = date('Y-m-d', strtotime('+25 days', $createdAt));
                $project->completed_at = date(
                    'Y-m-d H:i:s',
                    strtotime('+25 days', $createdAt)
                );
            } else {
                $project->end_date = date('Y-m-d', strtotime('+60 days', $createdAt));
            }

            /* ================= PROJECT MANAGER (RANDOM USER) ================= */
            if (!empty($userIds)) {
                $project->project_manager_id = $faker->randomElement($userIds);
            }

            /* ================= SAVE ================= */
            $project->save(false);
        }

        echo "✅ projects seeded with project manager (old + realistic)\n";
    }
}
