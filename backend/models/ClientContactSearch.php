<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ClientContact;

/**
 * ClientContactSearch model
 *
 * This model is used to search and filter ClientContact records
 * in the backend panel.
 */
class ClientContactSearch extends ClientContact
{
    /**
     * Validation rules for search attributes.
     */
    public function rules()
    {
        return [
            // Integer filters
            [['id', 'client_id', 'is_primary'], 'integer'],

            // Safe (searchable) fields
            [['name', 'designation', 'email', 'phone', 'created_at'], 'safe'],
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
        $query = ClientContact::find();

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
            'is_primary' => $this->is_primary,
            'created_at' => $this->created_at,
        ]);

        /* ===============================
            TEXT SEARCH FILTERS
        =============================== */

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'designation', $this->designation])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }
}
