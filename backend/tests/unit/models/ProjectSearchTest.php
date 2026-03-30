<?php

namespace backend\tests\unit\models;

use backend\models\ProjectSearch;
use common\models\Project;
use Yii;
use yii\data\ActiveDataProvider;
use Codeception\Test\Unit;

/**
 * Class ProjectSearchTest
 * @package backend\tests\unit\models
 * @covers \backend\models\ProjectSearch
 */
class ProjectSearchTest extends Unit
{
    /**
     * @var \backend\tests\UnitTester
     */
    protected $tester;

    /**
     * Test the rules method returns correct validation rules
     */
    public function testRules()
    {
        $searchModel = new ProjectSearch();
        
        $expectedRules = [
            [['id', 'client_id'], 'integer'],
            [['project_code', 'project_name', 'status', 'priority', 'start_date_range', 'end_date_range'], 'safe'],
        ];
        
        $rules = $searchModel->rules();
        
        $this->assertEquals($expectedRules, $rules);
    }

    /**
     * Test the scenarios method returns parent scenarios
     */
    public function testScenarios()
    {
        $searchModel = new ProjectSearch();

        // Since ProjectSearch extends Project model, it should have the same scenarios
        // but with additional attributes from ProjectSearch
        $actualScenarios = $searchModel->scenarios();

        // Check that the default scenario exists
        $this->assertArrayHasKey('default', $actualScenarios);
    }

    /**
     * Test search method with empty params
     */
    public function testSearchWithEmptyParams()
    {
        $searchModel = new ProjectSearch();
        
        // Since we can't easily mock ActiveRecord methods, we'll test the structure
        $dataProvider = $searchModel->search([]);
        
        $this->assertInstanceOf(ActiveDataProvider::class, $dataProvider);
    }

    /**
     * Test search method with filter params
     */
    public function testSearchWithFilters()
    {
        $searchModel = new ProjectSearch();
        
        // Test with ID filter
        $params = ['ProjectSearch' => ['id' => 1]];
        $dataProvider = $searchModel->search($params);
        
        $this->assertInstanceOf(ActiveDataProvider::class, $dataProvider);
    }

    /**
     * Test search method with client_id filter
     */
    public function testSearchWithClientIdFilter()
    {
        $searchModel = new ProjectSearch();
        
        $params = ['ProjectSearch' => ['client_id' => 5]];
        $dataProvider = $searchModel->search($params);
        
        $this->assertInstanceOf(ActiveDataProvider::class, $dataProvider);
    }

    /**
     * Test search method with project name filter
     */
    public function testSearchWithProjectNameFilter()
    {
        $searchModel = new ProjectSearch();
        
        $params = ['ProjectSearch' => ['project_name' => 'Test Project']];
        $dataProvider = $searchModel->search($params);
        
        $this->assertInstanceOf(ActiveDataProvider::class, $dataProvider);
    }

    /**
     * Test search method with project code filter
     */
    public function testSearchWithProjectCodeFilter()
    {
        $searchModel = new ProjectSearch();
        
        $params = ['ProjectSearch' => ['project_code' => 'PROJ-001']];
        $dataProvider = $searchModel->search($params);
        
        $this->assertInstanceOf(ActiveDataProvider::class, $dataProvider);
    }

    /**
     * Test search method with status filter
     */
    public function testSearchWithStatusFilter()
    {
        $searchModel = new ProjectSearch();
        
        $params = ['ProjectSearch' => ['status' => 'active']];
        $dataProvider = $searchModel->search($params);
        
        $this->assertInstanceOf(ActiveDataProvider::class, $dataProvider);
    }

    /**
     * Test search method with priority filter
     */
    public function testSearchWithPriorityFilter()
    {
        $searchModel = new ProjectSearch();
        
        $params = ['ProjectSearch' => ['priority' => 'high']];
        $dataProvider = $searchModel->search($params);
        
        $this->assertInstanceOf(ActiveDataProvider::class, $dataProvider);
    }

    /**
     * Test search method with date range filters
     */
    public function testSearchWithDateRange()
    {
        $searchModel = new ProjectSearch();
        
        $params = [
            'ProjectSearch' => [
                'start_date_range' => '2023-01-01 - 2023-12-31'
            ]
        ];
        $dataProvider = $searchModel->search($params);
        
        $this->assertInstanceOf(ActiveDataProvider::class, $dataProvider);
    }

    /**
     * Test search method with end date range filters
     */
    public function testSearchWithEndDateRange()
    {
        $searchModel = new ProjectSearch();
        
        $params = [
            'ProjectSearch' => [
                'end_date_range' => '2023-01-01 - 2023-12-31'
            ]
        ];
        $dataProvider = $searchModel->search($params);
        
        $this->assertInstanceOf(ActiveDataProvider::class, $dataProvider);
    }

    /**
     * Test attribute labels (inherited from parent)
     */
    public function testAttributeLabels()
    {
        $searchModel = new ProjectSearch();
        $labels = $searchModel->attributeLabels();
        
        // Since it extends Project model, it should inherit attribute labels
        $this->assertIsArray($labels);
    }

    /**
     * Test that ProjectSearch extends Project model
     */
    public function testExtendsProjectModel()
    {
        $searchModel = new ProjectSearch();
        
        $this->assertInstanceOf(Project::class, $searchModel);
    }

    /**
     * Test custom attributes exist
     */
    public function testCustomAttributes()
    {
        $searchModel = new ProjectSearch();
        
        $this->assertTrue(property_exists($searchModel, 'start_date_range'));
        $this->assertTrue(property_exists($searchModel, 'end_date_range'));
    }
}