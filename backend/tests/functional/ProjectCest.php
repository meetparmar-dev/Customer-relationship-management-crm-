<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\models\Project;
use common\models\Client;
use common\models\User;

class ProjectCest
{
    private function createClient()
    {
        $client = new Client();
        $client->client_code = 'CLT' . rand(1000, 9999);
        $client->type = Client::TYPE_INDIVIDUAL;
        $client->first_name = 'Test';
        $client->last_name = 'Client';
        $client->email = 'client' . rand(1, 9999) . '@test.com';
        $client->phone = '9999999999';
        $client->status = 1;
        $client->save(false);
        return $client;
    }

    private function createTestUser()
    {
        // Check if a test admin user already exists to avoid duplicates
        $user = User::findOne(['username' => 'test_admin']);
        if ($user) {
            return $user;
        }

        $user = new User();
        $user->username = 'test_admin';
        $user->email = 'admin@test.com';
        $user->first_name = 'Test';
        $user->last_name = 'Admin';
        $user->status = User::STATUS_ACTIVE;
        $user->role = User::ROLE_ADMIN;
        $user->setPassword('password123');
        $user->generateAuthKey();
        $user->save(false);

        return $user;
    }

    public function _before(FunctionalTester $I)
    {
        $user = $this->createTestUser();
        $I->amLoggedInAs($user->id);
    }

    public function testCreateProject(FunctionalTester $I)
    {
        $client = $this->createClient();

        $I->amOnPage('/project/create');

        $I->submitForm('form', [
            'Project[project_code]' => 'PRJ100',
            'Project[project_name]' => 'Test Project',
            'Project[client_id]'    => $client->id,
            'Project[status]'       => Project::STATUS_ACTIVE,
            'Project[priority]'     => Project::PRIORITY_HIGH,
        ]);

        $I->see('Project created successfully.');

        $I->seeRecord(Project::class, [
            'project_code' => 'PRJ100',
            'client_id' => $client->id,
        ]);
    }

    public function testUpdateProject(FunctionalTester $I)
    {
        $client = $this->createClient();

        $project = new Project([
            'project_code' => 'PRJ200',
            'project_name' => 'Old Name',
            'client_id' => $client->id,
            'status' => Project::STATUS_ACTIVE,
            'priority' => Project::PRIORITY_LOW,
        ]);
        $project->save(false);

        $I->amOnPage('/project/update?id=' . $project->id);

        $I->submitForm('form', [
            'Project[project_name]' => 'Updated Project',
        ]);

        $I->see('Project updated successfully.');

        $I->seeRecord(Project::class, [
            'id' => $project->id,
            'project_name' => 'Updated Project',
        ]);
    }

    public function testDeleteProject(FunctionalTester $I)
    {
        $client = $this->createClient();

        $project = new Project([
            'project_code' => 'PRJ300',
            'project_name' => 'Delete Me',
            'client_id' => $client->id,
            'status' => Project::STATUS_ACTIVE,
            'priority' => Project::PRIORITY_MEDIUM,
        ]);
        $project->save(false);

        // Yii2 way to send POST
        $I->sendAjaxPostRequest('/project/delete?id=' . $project->id);

        $I->dontSeeRecord(Project::class, [
            'id' => $project->id
        ]);
    }

    public function testChangeStatus(FunctionalTester $I)
    {
        $client = $this->createClient();

        $project = new Project([
            'project_code' => 'PRJ400',
            'project_name' => 'Status Test',
            'client_id' => $client->id,
            'status' => Project::STATUS_ACTIVE,
            'priority' => Project::PRIORITY_HIGH,
        ]);
        $project->save(false);

        // Call AJAX endpoint
        $I->sendAjaxPostRequest('/project/change-status', [
            'id' => $project->id,
            'status' => Project::STATUS_COMPLETED,
        ]);

        // THIS is the real test: did DB change?
        $I->seeRecord(Project::class, [
            'id' => $project->id,
            'status' => Project::STATUS_COMPLETED,
        ]);
    }
}
