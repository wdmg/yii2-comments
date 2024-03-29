<?php

namespace wdmg\comments\components;


/**
 * Yii2 Comments
 *
 * @category        Component
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-comments
 * @copyright       Copyright (c) 2019 - 2023 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use wdmg\helpers\ArrayHelper;

class Comments extends Component
{

    protected $module;
    protected $model;

    /**
     * Initialize the component
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $this->module = Yii::$app->getModule('comments');
        if (is_null($this->module))
            $this->module = Yii::$app->getModule('admin/comments');

        $this->model = new \wdmg\comments\models\Comments;

        parent::init();
    }

    public function get($context = 'default', $target = null) {

        if (is_null($context))
            return null;

        if (is_null($target) && ($request = Yii::$app->request->resolve())) {

            // Request route
            if (isset($request[0]))
                $target = $request[0];

        }

        if (is_null($target))
            return null;

        $this->model->setScenario($this->model::COMMENT_SCENARIO_LISTING);
        $query = $this->model::find()->where([
            'context' => $context,
            'target' => $target,
        ]);

        $query->andWhere([
            'or',
            ['status' => $this->model::COMMENT_STATUS_PUBLISHED],
            ['status' => $this->model::COMMENT_STATUS_DELETED]
        ]);

        if ($query->exists()) {
            if ($list = $query->asArray()->all()) {
                return [
                    'items' => ArrayHelper::buildTree($list),
                    'count' => count($list)
                ];
            }
        }

        return null;
    }

    public function getModel($context = 'default', $target = null, $newInstance = false) {

        if (is_null($context))
            return null;

        if (is_null($target) && ($request = Yii::$app->request->resolve())) {

            // Request route
            if (isset($request[0]))
                $target = $request[0];

        }

        if (is_null($target))
            return null;

        if ($newInstance) {
            $model = new \wdmg\comments\models\Comments;
            $model->setScenario($model::COMMENT_SCENARIO_CREATE);
        } else {
            $model = $this->model;
            $model->setScenario($model::COMMENT_SCENARIO_UPDATE);
        }

        $model->context = $context;
        $model->target = $target;
        $model->setScenario($model::COMMENT_SCENARIO_CREATE);
        return $model;
    }

    public function getModule() {
        return $this->module;
    }

}

?>