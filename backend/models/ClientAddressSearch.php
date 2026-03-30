<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ClientAddress;

/**
 * ClientAddressSearch model
 *
 * This model is used to search and filter ClientAddress records
 * in the backend panel.
 */
class ClientAddressSearch extends ClientAddress
{
    /**
     * Validation rules for search attributes.
     */
    public function rules()
    {
        return [
            // Integer filters
            [['id', 'client_id'], 'integer'],

            // Safe (searchable) fields
            [['address_type', 'address', 'city', 'state', 'country', 'pincode', 'created_at'], 'safe'],
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
        $query = ClientAddress::find();

        // Data provider
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

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
            'id' => $this->id,
            'client_id' => $this->client_id,
            'created_at' => $this->created_at,
        ]);

        /* ===============================
           TEXT SEARCH FILTERS
        =============================== */

        $query->andFilterWhere(['like', 'address_type', $this->address_type])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'state', $this->state])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'pincode', $this->pincode]);

        return $dataProvider;
    }

    /**
     * Custom validator to allow only one address per client.
     */
    public function validateSingleAddress($attribute)
    {
        $exists = self::find()
            ->andWhere(['client_id' => $this->client_id])
            ->andWhere(['!=', 'id', $this->id]) // For update case
            ->exists();

        if ($exists) {
            $this->addError($attribute, 'Only one address is allowed per client.');
        }
    }

    /**
     * API search with pagination
     *
     * @param array $params
     * @return array
     */
    public function searchApi($params)
    {
        $query = ClientAddress::find()->orderBy(['id' => SORT_DESC]);

        // Load params directly (API)
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
            'id'        => $this->id,
            'client_id' => $this->client_id,
        ]);

        /* ===============================
           TEXT SEARCH FILTERS
        =============================== */

        $query->andFilterWhere(['like', 'address_type', $this->address_type])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'state', $this->state])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'pincode', $this->pincode]);

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
                'has_more'     => $page < (int)ceil($total / $perPage),
            ],
        ];
    }
}
