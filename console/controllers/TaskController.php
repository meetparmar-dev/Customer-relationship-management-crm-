<?php

namespace console\controllers;

use yii\console\Controller;
use Faker\Factory;
use common\models\Project;
use common\models\Task;
use common\models\User;

class TaskController extends Controller
{
    // php yii task/seed 3
    public function actionSeed($tasksPerProject = 3)
    {
        $faker    = Factory::create('en_IN');
        $projects = Project::find()->all();

        // 👉 sab users ke IDs ek baar nikaal lo (performance + random)
        $userIds = User::find()
            ->select('id')
            ->column();

        foreach ($projects as $project) {

            for ($i = 0; $i < $tasksPerProject; $i++) {

                $task = new Task();

                /* ================= REQUIRED ================= */
                $task->project_id = $project->id;
                $task->title      = $faker->sentence(4);

                /* ================= OPTIONAL / RANDOM ================= */
                $task->description = $faker->paragraph;

                // enums (rule-safe)
                $task->status = $faker->randomElement([
                    'pending',
                    'in_progress',
                    'completed',
                ]);

                $task->priority = $faker->randomElement([
                    'low',
                    'medium',
                    'high',
                ]);

                // numbers
                $task->estimated_hours = $faker->numberBetween(1, 40);

                // dates
                $task->start_date = $faker
                    ->dateTimeBetween('-10 days', 'now')
                    ->format('Y-m-d');

                $task->due_date = $faker
                    ->dateTimeBetween('now', '+20 days')
                    ->format('Y-m-d');

                // completed_at only if completed
                if ($task->status === 'completed') {
                    $task->completed_at = $faker
                        ->dateTimeBetween('-5 days', 'now')
                        ->format('Y-m-d H:i:s');
                }

                /* ================= ASSIGNED TO (RANDOM USER) ================= */
                if (!empty($userIds)) {
                    $task->assigned_to = $faker->randomElement($userIds);
                }

                /**
                 * ❌ created_at / updated_at manually set nahi
                 * ✔ beforeSave() khud handle karega
                 */

                $task->save(false);
            }
        }

        echo "✅ tasks seeded successfully (assigned_to included)\n";
    }
}
