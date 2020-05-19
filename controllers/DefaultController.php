<?php

namespace wdmg\comments\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use wdmg\comments\models\Comments;

/**
 * DefaultController implements actions for Comments model.
 */
class DefaultController extends Controller
{
    /**
     * {@inheritdoc}
     */
    /*public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['GET', 'POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['*'],
                        'allow' => true
                    ],
                ],
            ],
        ];

        return $behaviors;
    }*/

    public function actionIndex($id = null) {

        if (isset($id))
            var_dump($id);

        echo "Index OK!";
        die();
    }

    public function actionCreate($id = null) {

        if (isset($id))
            var_dump($id);

        echo "Create OK!";
        die();
    }
    public function actionReply($id = null) {

        if (isset($id))
            var_dump($id);

        echo "Reply OK!";
        die();
    }
    public function actionDelete($id = null) {

        if (isset($id))
            var_dump($id);

        echo "Delete OK!";
        die();
    }
    public function actionAbuse($id = null) {

        if (isset($id))
            var_dump($id);

        echo "Abuse OK!";
        die();
    }
}
