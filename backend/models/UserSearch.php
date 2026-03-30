<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use yii\db\Expression;

class UserSearch extends User
{
    public $full_name;
    public $created_at_range;

    public function rules()
    {
        return [
            [['id', 'status', 'role'], 'integer'],
            [['first_name', 'last_name', 'username', 'email', 'full_name', 'created_at_range'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params, $formName = null)
    {
        $query = User::find();

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

        // Enable sorting for full_name
        $dataProvider->sort->attributes['full_name'] = [
            'asc'  => new Expression("CONCAT(first_name, ' ', last_name) ASC"),
            'desc' => new Expression("CONCAT(first_name, ' ', last_name) DESC"),
        ];

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        /* ========== EXACT MATCH FILTERS ========== */
        $query->andFilterWhere([
            'id'     => $this->id,
            'status' => $this->status,
            'role'   => $this->role,
        ]);

        /* ========== DATE RANGE FILTER ========== */
        if (!empty($this->created_at_range)) {
            $dates = explode(' - ', $this->created_at_range);

            if (count($dates) == 2) {
                $start = strtotime($dates[0] . ' 00:00:00');
                $end   = strtotime($dates[1] . ' 23:59:59');

                $query->andFilterWhere(['between', 'created_at', $start, $end]);
            }
        }

        /* ========== LIKE SEARCH ========== */
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere([
                'like',
                new Expression("CONCAT(first_name, ' ', last_name)"),
                $this->full_name
            ]);

        return $dataProvider;
    }

    /**
     * API search with pagination & global search
     *
     * @param array $params
     * @return array
     */
    public function searchApi($params)
    {
        $query = User::find()->orderBy(['id' => SORT_DESC]);

        // Load API params
        $this->load($params, '');

        // Global search (search=...)
        $search = $params['search'] ?? null;
        if (!empty($search)) {
            $query->andWhere([
                'OR',
                ['like', 'username', $search],
                ['like', 'email', $search],
                ['like', 'first_name', $search],
                ['like', 'last_name', $search],
            ]);
        }

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

        /* ========== EXACT MATCH FILTERS ========== */
        $query->andFilterWhere([
            'id'     => $this->id,
            'status' => $this->status,
            'role'   => $this->role,
        ]);

        /* ========== DATE RANGE FILTER ========== */
        if (!empty($this->created_at_range)) {
            $dates = explode(' - ', $this->created_at_range);

            if (count($dates) === 2) {
                $start = strtotime($dates[0] . ' 00:00:00');
                $end   = strtotime($dates[1] . ' 23:59:59');

                $query->andFilterWhere(['between', 'created_at', $start, $end]);
            }
        }

        /* ========== FIELD SEARCH ========== */
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere([
                'like',
                new Expression("CONCAT(first_name, ' ', last_name)"),
                $this->full_name
            ]);

        // Total count
        $total = $query->count();

        // Fetch paginated data
        $data = $query
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->asArray()
            ->all();

        // Remove sensitive fields 🔐
        foreach ($data as &$user) {
            unset(
                $user['password_hash'],
                $user['auth_key'],
                $user['password_reset_token'],
                $user['verification_token']
            );
        }

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
