<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Task;

class TaskSearch extends Task
{

    public $due_date_range;


    public function rules()
    {
        return [

            [['due_date_range'], 'safe'],

            [['id', 'project_id', 'assigned_to'], 'integer'],
            [['title', 'status', 'priority'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params, $formName = null)
    {
        $query = Task::find()
            ->with(['project', 'assignee']);

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

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!empty($this->due_date_range)) {
            [$start, $end] = explode(' - ', $this->due_date_range);

            $query->andFilterWhere([
                'between',
                'due_date',
                date('Y-m-d', strtotime($start)),
                date('Y-m-d', strtotime($end)),
            ]);
        }


        /* ===== EXACT FILTER ===== */
        $query->andFilterWhere([
            'id' => $this->id,
            'project_id' => $this->project_id,
            'assigned_to' => $this->assigned_to,
            'status' => $this->status,
            'priority' => $this->priority,
        ]);

        /* ===== LIKE SEARCH ===== */
        $query->andFilterWhere(['like', 'title', $this->title]);

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
        $query = Task::find()
            ->with(['project', 'assignee'])
            ->orderBy(['id' => SORT_DESC]);

        // Load API params
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
           DATE RANGE FILTER
        =============================== */
        if (!empty($this->due_date_range)) {
            [$start, $end] = explode(' - ', $this->due_date_range);

            $query->andFilterWhere([
                'between',
                'due_date',
                date('Y-m-d', strtotime($start)),
                date('Y-m-d', strtotime($end)),
            ]);
        }

        /* ===============================
           EXACT FILTERS
        =============================== */
        $query->andFilterWhere([
            'id'          => $this->id,
            'project_id'  => $this->project_id,
            'assigned_to' => $this->assigned_to,
            'status'      => $this->status,
            'priority'    => $this->priority,
        ]);

        /* ===============================
           LIKE SEARCH
        =============================== */
        $query->andFilterWhere(['like', 'title', $this->title]);

        // Total count
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
