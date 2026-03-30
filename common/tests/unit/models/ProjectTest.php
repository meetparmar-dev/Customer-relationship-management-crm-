<?php

namespace common\tests\unit\models;

use common\models\Project;
use common\models\Client;
use Codeception\Test\Unit;

class ProjectTest extends Unit
{
    private function createClient()
    {
        $client = new Client();
        $client->client_code = 'CL-' . time();
        $client->company_name = 'Test Company';
        $client->email = 'client' . time() . '@test.com';
        $client->phone = '9999999999';
        $client->status = 1;
        $client->save(false);

        return $client;
    }

    public function testRequiredFields()
    {
        $project = new Project();
        $this->assertFalse($project->validate());
        $this->assertArrayHasKey('project_name', $project->errors);
        $this->assertArrayHasKey('client_id', $project->errors);
    }

    public function testCreateValidProject()
    {
        $client = $this->createClient();

        $project = new Project([
            'project_name' => 'CRM System',
            'client_id' => $client->id,
            'status' => Project::STATUS_ACTIVE,
            'priority' => Project::PRIORITY_HIGH,
            'billing_type' => Project::BILLING_FIXED,
            'budget' => 50000,
        ]);

        $this->assertTrue($project->save());

        $this->assertNotEmpty($project->project_code);
        $this->assertNotEmpty($project->created_at);
        $this->assertNotEmpty($project->updated_at);
    }

    public function testInvalidStatus()
    {
        $client = $this->createClient();

        $project = new Project([
            'project_name' => 'Invalid Status Project',
            'client_id' => $client->id,
            'status' => 'wrong_status',
        ]);

        $this->assertFalse($project->validate());
        $this->assertArrayHasKey('status', $project->errors);
    }

    public function testInvalidPriority()
    {
        $client = $this->createClient();

        $project = new Project([
            'project_name' => 'Invalid Priority',
            'client_id' => $client->id,
            'priority' => 'urgent',
        ]);

        $this->assertFalse($project->validate());
        $this->assertArrayHasKey('priority', $project->errors);
    }

    public function testBillingTypeValidation()
    {
        $client = $this->createClient();

        $project = new Project([
            'project_name' => 'Billing Test',
            'client_id' => $client->id,
            'billing_type' => 'monthly',
        ]);

        $this->assertFalse($project->validate());
        $this->assertArrayHasKey('billing_type', $project->errors);
    }

    public function testUniqueProjectCode()
    {
        $client = $this->createClient();

        $p1 = new Project([
            'project_name' => 'Project 1',
            'client_id' => $client->id,
            'project_code' => 'PRJ-001',
        ]);
        $p1->save(false);

        $p2 = new Project([
            'project_name' => 'Project 2',
            'client_id' => $client->id,
            'project_code' => 'PRJ-001',
        ]);

        $this->assertFalse($p2->validate());
        $this->assertArrayHasKey('project_code', $p2->errors);
    }

    public function testRelations()
    {
        $client = $this->createClient();

        $project = new Project([
            'project_name' => 'Relation Test',
            'client_id' => $client->id,
        ]);
        $project->save(false);

        $this->assertEquals($client->id, $project->client->id);
    }

    public function testVirtualNameProperty()
    {
        $client = $this->createClient();

        $project = new Project([
            'name' => 'Virtual Name Project',
            'client_id' => $client->id,
        ]);

        $this->assertEquals('Virtual Name Project', $project->project_name);
        $this->assertEquals('Virtual Name Project', $project->name);
    }

    public function testCompletedAtAllowed()
    {
        $client = $this->createClient();

        $project = new Project([
            'project_name' => 'Completed Project',
            'client_id' => $client->id,
            'completed_at' => '2026-01-01',
        ]);

        $this->assertTrue($project->validate());
    }
}
