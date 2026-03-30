<?php

namespace common\tests\unit\models;

use common\models\Task;
use common\models\Project;
use common\models\User;
use common\models\Client;
use Codeception\Test\Unit;

class TaskTest extends Unit
{
    protected function createClient()
    {
        $client = new Client();
        $client->type = Client::TYPE_INDIVIDUAL;
        $client->first_name = 'Test';
        $client->last_name = 'Client';
        $client->email = 'test' . time() . '@example.com';
        $client->phone = '+1234567890';
        $client->save(false);

        return $client;
    }

    protected function createProject()
    {
        $client = $this->createClient();

        $project = new Project();
        $project->project_name = 'Test Project ' . time();
        $project->client_id = $client->id;
        $project->status = Project::STATUS_ACTIVE;
        $project->priority = Project::PRIORITY_MEDIUM;
        $project->save(false);

        return $project;
    }

    protected function createUser()
    {
        $user = new User();
        $user->username = 'task_user_' . time();
        $user->email = 'task' . time() . '@test.com';
        $user->setPassword('password123');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->save(false);

        return $user;
    }

    /** ✅ Required fields */
    public function testRequiredFields()
    {
        $task = new Task();
        $this->assertFalse($task->validate(['project_id', 'title']));
    }

    /** ✅ Create valid task */
    public function testCreateValidTask()
    {
        $project = $this->createProject();

        $task = new Task();
        $task->project_id = $project->id;
        $task->title = 'Test Task';
        $task->description = 'Testing task';
        $this->assertTrue($task->save());

        $this->assertNotNull($task->id);
        $this->assertEquals(Task::STATUS_PENDING, $task->status);
        $this->assertEquals(Task::PRIORITY_MEDIUM, $task->priority);
    }

    /** ✅ completed_at auto set */
    public function testCompletedAtIsSet()
    {
        $project = $this->createProject();

        $task = new Task();
        $task->project_id = $project->id;
        $task->title = 'Finish work';
        $task->status = Task::STATUS_COMPLETED;
        $task->save();

        $this->assertNotNull($task->completed_at);
    }

    /** ✅ completed_at resets when status changes */
    public function testCompletedAtResets()
    {
        $project = $this->createProject();

        $task = new Task();
        $task->project_id = $project->id;
        $task->title = 'Test Reset';
        $task->status = Task::STATUS_COMPLETED;
        $task->save();

        $this->assertNotNull($task->completed_at);

        $task->status = Task::STATUS_PENDING;
        $task->save();

        $this->assertNull($task->completed_at);
    }

    /** ✅ Project relation */
    public function testProjectRelation()
    {
        $project = $this->createProject();

        $task = new Task();
        $task->project_id = $project->id;
        $task->title = 'Relation test';
        $task->save();

        $this->assertEquals($project->id, $task->project->id);
    }

    /** ✅ Assignee relation */
    public function testAssigneeRelation()
    {
        $project = $this->createProject();
        $user = $this->createUser();

        $task = new Task();
        $task->project_id = $project->id;
        $task->assigned_to = $user->id;
        $task->title = 'Assigned Task';
        $task->save();

        $this->assertEquals($user->id, $task->assignee->id);
    }

    /** ✅ Status list */
    public function testStatusList()
    {
        $list = Task::statusList();
        $this->assertArrayHasKey(Task::STATUS_PENDING, $list);
        $this->assertArrayHasKey(Task::STATUS_COMPLETED, $list);
    }

    /** ✅ Priority list */
    public function testPriorityList()
    {
        $list = Task::priorityList();
        $this->assertArrayHasKey(Task::PRIORITY_LOW, $list);
        $this->assertArrayHasKey(Task::PRIORITY_HIGH, $list);
    }
}
