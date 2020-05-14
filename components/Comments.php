<?php

namespace wdmg\comments\components;


/**
 * Yii2 Comments
 *
 * @category        Component
 * @version         0.0.11
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-comments
 * @copyright       Copyright (c) 2019 - 2020 W.D.M.Group, Ukraine
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

        $query = $this->model::find()->where([
            'context' => $context,
            'target' => $target,
            'status' => $this->model::COMMENT_STATUS_PUBLISHED
        ]);

        if ($query->exists()) {
            $list = $query->asArray()->all();
            return ArrayHelper::buildTree($list);
        } else {
            return null;
        }
    }
}

?>