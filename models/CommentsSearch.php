<?php

namespace wdmg\comments\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use wdmg\comments\models\Comments;

/**
 * CommentsSearch represents the model behind the search form of `wdmg\comments\models\Comments`.
 */
class CommentsSearch extends Comments
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'user_id', 'is_published'], 'integer'],
            [['name', 'email', 'phone', 'photo', 'condition', 'comment', 'created_at', 'updated_at', 'session'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Comments::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_published' => $this->is_published,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'photo', $this->photo])
            ->andFilterWhere(['like', 'condition', $this->condition])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'session', $this->session]);

        return $dataProvider;
    }
}
