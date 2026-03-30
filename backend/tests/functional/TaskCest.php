<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\models\User;
use common\models\Task;
use common\models\Project;
use common\models\Client;

class TaskCest
{
    private function login(FunctionalTester $I)
    {
        $user = new User();
        $user->username = 'admin_' . time();
        $user->email = 'admin' . time() . '@test.com';
        $user->setPassword('password123');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->role = User::ROLE_ADMIN;
        $user->save(false);

        $I->amLoggedInAs($user->id);
    }

    public function indexPageWorks(FunctionalTester $I)
    {
        $this->login($I);

        $I->amOnPage('task/index');
        $I->see('Tasks');
    }

    public function viewPageWorks(FunctionalTester $I)
    {
        $this->login($I);

        $client = new Client([
            'client_code' => 'CLT-' . time(),
            'type' => Client::TYPE_INDIVIDUAL,
            'first_name' => 'Test Client',
            'email' => 'test' . time() . '@example.com',
            'phone' => '1234567890',
        ]);
        $client->save(false);

        $project = new Project([
            'project_name' => 'Test Project',
            'description' => 'Test project',
            'client_id' => $client->id,
            'status' => 'active',
        ]);
        $project->save(false);

        $task = new Task([
            'project_id' => $project->id,
            'title' => 'Test Task',
            'description' => 'Test task description',
            'status' => Task::STATUS_PENDING,
        ]);
        $task->save(false);

        $I->amOnPage('task/view?id=' . $task->id);
        $I->see('Test Task');
    }

    public function createTaskWorks(FunctionalTester $I)
    {
        $this->login($I);

        $client = new Client([
            'client_code' => 'CLT-' . time(),
            'type' => Client::TYPE_INDIVIDUAL,
            'first_name' => 'Client A',
            'email' => 'a' . time() . '@example.com',
            'phone' => '1234567890',
        ]);
        $client->save(false);

        $project = new Project([
            'project_name' => 'Project A',
            'description' => 'Test',
            'client_id' => $client->id,
            'status' => 'active',
        ]);
        $project->save(false);

        $I->amOnPage('task/create');

        $I->submitForm('form', [
            'Task[project_id]' => $project->id,
            'Task[title]' => 'Task Created',
            'Task[description]' => 'Created from test',
            'Task[status]' => Task::STATUS_PENDING,
        ]);

        $I->seeRecord(Task::class, [
            'title' => 'Task Created',
            'project_id' => $project->id
        ]);
    }

    public function updateTaskWorks(FunctionalTester $I)
    {
        $this->login($I);

        $client = new Client([
            'client_code' => 'CLT-' . time(),
            'type' => Client::TYPE_INDIVIDUAL,
            'first_name' => 'Client B',
            'email' => 'b' . time() . '@example.com',
            'phone' => '1234567890',
        ]);
        $client->save(false);

        $project = new Project([
            'project_name' => 'Project B',
            'description' => 'Test',
            'client_id' => $client->id,
            'status' => 'active',
        ]);
        $project->save(false);

        $task = new Task([
            'project_id' => $project->id,
            'title' => 'Old Title',
            'description' => 'Old desc',
            'status' => Task::STATUS_PENDING,
        ]);
        $task->save(false);

        $I->amOnPage('task/update?id=' . $task->id);

        $I->submitForm('form', [
            'Task[title]' => 'New Title',
            'Task[status]' => Task::STATUS_IN_PROGRESS,
        ]);

        $I->seeRecord(Task::class, [
            'id' => $task->id,
            'title' => 'New Title'
        ]);
    }

    public function deleteTaskWorks(FunctionalTester $I)
    {
        $this->login($I);

        $client = new Client([
            'client_code' => 'CLT-' . time(),
            'type' => Client::TYPE_INDIVIDUAL,
            'first_name' => 'Client C',
            'email' => 'c' . time() . '@example.com',
            'phone' => '1234567890',
        ]);
        $client->save(false);

        $project = new Project([
            'project_name' => 'Project C',
            'description' => 'Test',
            'client_id' => $client->id,
            'status' => 'active',
        ]);
        $project->save(false);

        $task = new Task([
            'project_id' => $project->id,
            'title' => 'Delete Me',
            'status' => Task::STATUS_PENDING,
        ]);
        $task->save(false);

        $I->sendAjaxPostRequest('/task/delete?id=' . $task->id);

        $I->dontSeeRecord(Task::class, [
            'id' => $task->id
        ]);
    }

    public function changeStatusWorks(FunctionalTester $I)
    {
        $this->login($I);

        $client = new Client([
            'client_code' => 'CLT-' . time(),
            'type' => Client::TYPE_INDIVIDUAL,
            'first_name' => 'Client D',
            'email' => 'd' . time() . '@example.com',
            'phone' => '1234567890',
        ]);
        $client->save(false);

        $project = new Project([
            'project_name' => 'Project D',
            'description' => 'Test',
            'client_id' => $client->id,
            'status' => 'active',
        ]);
        $project->save(false);

        $task = new Task([
            'project_id' => $project->id,
            'title' => 'Status Task',
            'status' => Task::STATUS_PENDING,
        ]);
        $task->save(false);

        $I->sendAjaxPostRequest('/task/change-status', [
            'id' => $task->id,
            'status' => Task::STATUS_IN_PROGRESS
        ]);

        $I->seeRecord(Task::class, [
            'id' => $task->id,
            'status' => Task::STATUS_IN_PROGRESS
        ]);
    }

    public function unauthorizedUserCannotAccessTaskPages(FunctionalTester $I)
    {
        $I->amOnPage('task/index');
        $I->see('Login');
    }
}
