<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Project;

/**
 * ProjectSearch model
 *
 * This model is used to search and filter Project records
 * in the backend panel. It supports filtering by date ranges,
 * client, status, priority, and project details.
 */
class ProjectSearch extends Project
{
    /**
     * Date range for filtering project start date.
     */
    public $start_date_range;

    /**
     * Date range for filtering project end date.
     */
    public $end_date_range;

    /**
     * Validation rules for search attributes.
     */
    public function rules()
    {
        return [
            // Integer filters
            [['id', 'client_id'], 'integer'],

            // Safe (searchable) fields
            [
                [
                    'project_code',
                    'project_name',
                    'status',
                    'priority',
                    'start_date_range',
                    'end_date_range',
                ],
                'safe'
            ],
        ];
    }

    /**
     * Scenarios are not required for search model.
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @param array       $params
     * @param string|null $formName
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        // Base query
        $query = Project::find();

        // Data provider configuration
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        // Load search parameters
        $this->load($params, $formName);

        // If validation fails, return unfiltered data
        if (!$this->validate()) {
            return $dataProvider;
        }

        /* ===============================
           DATE RANGE FILTERS
        =============================== */

        // Filter by project start date range
        if (!empty($this->start_date_range)) {
            [$start, $end] = explode(' - ', $this->start_date_range);

            $query->andFilterWhere([
                'between',
                'start_date',
                date('Y-m-d', strtotime($start)),
                date('Y-m-d', strtotime($end)),
            ]);
        }

        // Filter by project end date range
        if (!empty($this->end_date_range)) {
            [$start, $end] = explode(' - ', $this->end_date_range);

            $query->andFilterWhere([
                'between',
                'end_date',
                date('Y-m-d', strtotime($start)),
                date('Y-m-d', strtotime($end)),
            ]);
        }

        /* ===============================
           EXACT MATCH FILTERS
        =============================== */

        $query->andFilterWhere([
            'id'        => $this->id,
            'client_id' => $this->client_id,
            'status'    => $this->status,
            'priority'  => $this->priority,
        ]);

        /* ===============================
           TEXT SEARCH FILTERS
        =============================== */

        $query->andFilterWhere(['like', 'project_code', $this->project_code])
            ->andFilterWhere(['like', 'project_name', $this->project_name]);

        return $dataProvider;
    }

    /**
     * API search with pagination
     *
     * @param array $params
     * @return array
     */
    public function searchApi($params)
    {
        $query = Project::find()->orderBy(['id' => SORT_DESC]);

        // API params load
        $this->load($params, '');

        // Pagination
        $page    = max((int)($params['page'] ?? 1), 1);
        $perPage = min(max((int)($params['per_page'] ?? 10), 1), 100);

        if (!$this->validate()) {
            return [
                'data' => [],
                'pagination' => [
                    'total' => 0,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => 0,
                    'has_more' => false,
                ],
            ];
        }

        /* ===============================
           DATE RANGE FILTERS
        =============================== */

        if (!empty($this->start_date_range)) {
            [$start, $end] = explode(' - ', $this->start_date_range);

            $query->andFilterWhere([
                'between',
                'start_date',
                date('Y-m-d', strtotime($start)),
                date('Y-m-d', strtotime($end)),
            ]);
        }

        if (!empty($this->end_date_range)) {
            [$start, $end] = explode(' - ', $this->end_date_range);

            $query->andFilterWhere([
                'between',
                'end_date',
                date('Y-m-d', strtotime($start)),
                date('Y-m-d', strtotime($end)),
            ]);
        }

        /* ===============================
           EXACT MATCH FILTERS
        =============================== */

        $query->andFilterWhere([
            'id'        => $this->id,
            'client_id' => $this->client_id,
            'status'    => $this->status,
            'priority'  => $this->priority,
        ]);

        /* ===============================
           TEXT SEARCH FILTERS
        =============================== */

        $query->andFilterWhere(['like', 'project_code', $this->project_code])
            ->andFilterWhere(['like', 'project_name', $this->project_name]);

        // Total count (before limit)
        $total = $query->count();

        // Fetch paginated data
        $data = $query
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->asArray()
            ->all();

        return [
            'data' => $data,
            'pagination' => [
                'total'        => $total,
                'per_page'     => $perPage,
                'current_page' => $page,
                'last_page'    => (int)ceil($total / $perPage),
                'has_more'     => $page < (int)ceil($total / $perPage),
            ],
        ];
    }
}
