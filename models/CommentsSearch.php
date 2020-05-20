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

    public $range;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'user_id', 'status'], 'integer'],
            [['context', 'target', 'name', 'email', 'comment', 'range'], 'string'],
            [['created_at', 'updated_at', 'session'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $scenarios = Model::scenarios();
        $scenarios['grouped'] = ['context', 'comment', 'target', 'range'];

        return $scenarios;
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
            /*'sort'=> [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]*/
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }/* else {
            // query all without languages version
            $query->where([
                'parent_id' => null,
            ]);
        }*/

        if ($this->scenario == 'grouped')
            $query->select("*, COUNT(*) as count");

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'session' => $this->session,
        ]);

        if ($this->context !== "*")
            $query->andFilterWhere(['context' => $this->context]);

        if ($this->target !== "*")
            $query->andFilterWhere(['target' => $this->target]);

        if ($this->status !== "*")
            $query->andFilterWhere(['status' => $this->status]);


        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        // Filter by range
        if ($this->scenario == 'grouped' && $this->range !== "*") {
            switch ($this->range) {
                case '< 1000' :
                    $query->having(['<', 'count', 1000]);
                    break;

                case '>= 1000' :
                    $query->having(['>=', 'count', 1000]);
                    break;

                case '>= 10000' :
                    $query->having(['>=', 'count', (1000 * 10)]);
                    break;

                case '> 100000' :
                    $query->having(['>', 'count', (1000 * 100)]);
                    break;

                case '> 1000000' :
                    $query->having(['>', 'count', (1000 * 1000)]);
                    break;

                case '> 10000000' :
                    $query->having(['>', 'count', (1000 * 1000 * 10)]);
                    break;
            }
        }

        if ($this->scenario == 'grouped') {
            $query->groupBy(['context', 'target'])->orderBy('updated_at', SORT_DESC);
        } else {
            $query->groupBy(['id', 'parent_id'])->orderBy('updated_at', SORT_DESC);
        }

        return $dataProvider;
    }
}
