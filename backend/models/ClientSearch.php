<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Client;

/**
 * ClientSearch model
 *
 * This model is used to search and filter Client records
 * in the backend panel. It also supports full name searching.
 */
class ClientSearch extends Client
{
    /**
     * Virtual attribute for full name search.
     */
    public $full_name;

    /**
     * Validation rules for search attributes.
     */
    public function rules()
    {
        return [
            // Integer filters
            [['id'], 'integer'],

            // Safe (searchable) fields
            [
                [
                    'client_code',
                    'type',
                    'company_name',
                    'first_name',
                    'last_name',
                    'full_name',
                    'email',
                    'phone',
                    'status',
                    'created_at',
                    'updated_at',
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
        // Bypass parent scenarios
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
        $query = Client::find();

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

        /**
         * Custom sorting for full name.
         */
        $dataProvider->sort->attributes['full_name'] = [
            'asc' => [
                'first_name' => SORT_ASC,
                'last_name'  => SORT_ASC,
            ],
            'desc' => [
                'first_name' => SORT_DESC,
                'last_name'  => SORT_DESC,
            ],
        ];

        // Load search parameters
        $this->load($params, $formName);

        // If validation fails, return unfiltered data
        if (!$this->validate()) {
            return $dataProvider;
        }

        /* ===============================
            EXACT MATCH FILTERS
        =============================== */

        $query->andFilterWhere([
            'id'     => $this->id,
            'status' => $this->status,
            'type'   => $this->type,
        ]);

        /* ===============================
            TEXT SEARCH FILTERS
        =============================== */

        $query->andFilterWhere(['like', 'client_code', $this->client_code])
            ->andFilterWhere(['like', 'company_name', $this->company_name]);

        /* ===============================
            FULL NAME SEARCH
        =============================== */

        if (!empty($this->full_name)) {
            $query->andWhere([
                'or',
                ['like', 'first_name', $this->full_name],
                ['like', 'last_name', $this->full_name],
                ['like', "CONCAT(first_name, ' ', last_name)", $this->full_name],
            ]);
        }

        return $dataProvider;
    }

    /**
     * API search with manual pagination
     *
     * @param array $params
     * @return array
     */
    public function searchApi($params)
    {
        $query = Client::find()->orderBy(['id' => SORT_DESC]);

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
            EXACT MATCH FILTERS
        =============================== */
        $query->andFilterWhere([
            'id'     => $this->id,
            'status' => $this->status,
            'type'   => $this->type,
        ]);

        /* ===============================
            TEXT SEARCH FILTERS
        =============================== */
        $query->andFilterWhere(['like', 'client_code', $this->client_code])
            ->andFilterWhere(['like', 'company_name', $this->company_name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name]);

        /* ===============================
            FULL NAME SEARCH
        =============================== */
        if (!empty($this->full_name)) {
            $query->andWhere([
                'or',
                ['like', 'first_name', $this->full_name],
                ['like', 'last_name', $this->full_name],
                ['like', "CONCAT(first_name, ' ', last_name)", $this->full_name],
            ]);
        }

        $total = $query->count();

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
                'has_more'     => $page * $perPage < $total,
            ],
        ];
    }
}
