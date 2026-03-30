<?php

namespace backend\tests\unit\models;

use backend\models\TaskSearch;
use Codeception\Test\Unit;
use common\models\Task;
use common\models\Project;
use common\models\User;

class TaskSearchTest extends Unit
{
    protected function _before()
    {
        // Create project - need to create a client first due to foreign key constraint
        $client = new \common\models\Client();
        $client->client_code = 'CLI-TEST';
        $client->type = \common\models\Client::TYPE_COMPANY;
        $client->first_name = 'Test';
        $client->email = 'test@example.com';
        $client->phone = '1234567890';
        $client->status = \common\models\Client::STATUS_ACTIVE;
        $client->company_name = 'Test Client';
        $client->save(false);

        $project = new Project([
            'project_name' => 'Test Project',
            'client_id' => $client->id
        ]);
        $project->save(false);

        // Create user
        $user = new User([
            'username' => 'task_user',
            'email' => 'task@test.com',
            'status' => User::STATUS_ACTIVE
        ]);
        $user->setPassword('123456');
        $user->generateAuthKey();
        $user->save(false);

        // Create tasks
        Task::deleteAll();

        $task1 = new Task([
            'title' => 'Design Homepage',
            'project_id' => $project->id,
            'assigned_to' => $user->id,
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => '2026-01-10'
        ]);
        $task1->save(false);

        $task2 = new Task([
            'title' => 'Fix bugs',
            'project_id' => $project->id,
            'assigned_to' => $user->id,
            'status' => 'completed',
            'priority' => 'low',
            'due_date' => '2026-01-20'
        ]);
        $task2->save(false);
    }

    public function testSearchWithoutFiltersReturnsAll()
    {
        $search = new TaskSearch();
        $dataProvider = $search->search([]);

        $this->assertEquals(2, $dataProvider->getTotalCount());
    }

    public function testSearchByTitle()
    {
        $search = new TaskSearch();
        $dataProvider = $search->search([
            'TaskSearch' => [
                'title' => 'Design'
            ]
        ]);

        $models = $dataProvider->getModels();

        $this->assertCount(1, $models);
        $this->assertEquals('Design Homepage', $models[0]->title);
    }

    public function testSearchByStatus()
    {
        $search = new TaskSearch();
        $dataProvider = $search->search([
            'TaskSearch' => [
                'status' => 'completed'
            ]
        ]);

        $this->assertEquals(1, $dataProvider->getTotalCount());
    }

    public function testSearchByPriority()
    {
        $search = new TaskSearch();
        $dataProvider = $search->search([
            'TaskSearch' => [
                'priority' => 'high'
            ]
        ]);

        $models = $dataProvider->getModels();

        $this->assertCount(1, $models);
        $this->assertEquals('high', $models[0]->priority);
    }

    public function testSearchByDueDateRange()
    {
        $search = new TaskSearch();
        $dataProvider = $search->search([
            'TaskSearch' => [
                'due_date_range' => '2026-01-01 - 2026-01-15'
            ]
        ]);

        $models = $dataProvider->getModels();

        $this->assertCount(1, $models);
        $this->assertEquals('Design Homepage', $models[0]->title);
    }
}
