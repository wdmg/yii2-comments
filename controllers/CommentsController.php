<?php

namespace wdmg\comments\controllers;

use wdmg\comments\widgets\CommentsWidget;
use Yii;
use wdmg\comments\models\Comments;
use wdmg\comments\models\CommentsSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * CommentsController implements the CRUD actions for Comments model.
 */
class CommentsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['admin'],
                        'allow' => true
                    ],
                ],
            ],
        ];

        // If auth manager not configured use default access control
        if(!Yii::$app->authManager) {
            $behaviors['access'] = [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true
                    ],
                ]
            ];
        }

        return $behaviors;
    }

    /**
     * Lists all Comments models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CommentsSearch();
        $searchModel->scenario = "grouped";
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionList($context = null, $target = null, $status = null)
    {
        if (is_null($context) || is_null($target))
            $this->redirect(['comments/index']);

        $searchModel = new CommentsSearch();
        $params = Yii::$app->request->queryParams;
        if (!isset($params['CommentsSearch'])) {
            $params['CommentsSearch'] = Yii::$app->request->queryParams;
        }
        $dataProvider = $searchModel->search($params);

        return $this->render('list', [
            'context' => $context,
            'target' => $target,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Comments model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_view', [
                'model' => $this->findModel($id),
            ]);
        }

        return $this->redirect(['comments/index']);
    }

    /**
     * Updates an existing Comments model.
     * If update is successful, the browser will be redirected to the 'comments/list' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['comments/list'], [
                'context' => $model->context,
                'target' => $model->target
            ]);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        }

        return $this->redirect(['comments/list']);
    }

    /**
     * Deletes an existing Comments model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $context = null, $target = null)
    {
        Comments::deleteAll(['parent_id' => $id]);
        Comments::deleteAll(['id' => $id]);
        return $this->redirect(['comments/list', 'context' => $context, 'target' => $target]);
    }

    public function actionBatch($action = null, $attribute = null, $context = null, $target = null, $value = null)
    {
        if (Yii::$app->request->isPost) {
            $selection = Yii::$app->request->post('selected', null);
            if (!is_null($selection)) {
                if ($action == 'change' && !is_null($attribute)) {

                    $updated = Comments::updateAll([$attribute => intval($value)], ['id' => $selection]);
                    if ($updated) {
                        // Log activity
                        $this->module->logActivity(
                            $updated . ' comment(s) successfully updated.',
                            $this->uniqueId . ":" . $this->action->id,
                            'success',
                            1
                        );

                        Yii::$app->getSession()->setFlash(
                            'success',
                            Yii::t(
                                'app/modules/comments',
                                'OK! {count, number} {count, plural, one{comment} few{comments} other{comments}} successfully {count, plural, one{updated} few{updated} other{updated}}.',
                                [
                                    'count' => $updated
                                ]
                            )
                        );
                    } else {
                        // Log activity
                        $this->module->logActivity(
                            'An error occurred while updating a comment(s).',
                            $this->uniqueId . ":" . $this->action->id,
                            'danger',
                            1
                        );

                        Yii::$app->getSession()->setFlash(
                            'danger',
                            Yii::t(
                                'app/modules/comments',
                                'An error occurred while updating a comment(s).'
                            )
                        );
                    }

                } elseif ($action == 'delete') {

                    $deleted = 0;

                    $models = Comments::findAll(['parent_id' => $selection]);
                    foreach($models as $model) {
                        if ($model->delete())
                            $deleted++;
                    }

                    $models = Comments::findAll(['id' => $selection]);
                    foreach($models as $model) {

                        if ($model->delete())
                            $deleted++;
                    }

                    if ($deleted) {
                        // Log activity
                        $this->module->logActivity(
                            $deleted . ' comment(s) successfully deleted.',
                            $this->uniqueId . ":" . $this->action->id,
                            'success',
                            1
                        );

                        Yii::$app->getSession()->setFlash(
                            'success',
                            Yii::t(
                                'app/modules/comments',
                                'OK! {count, number} {count, plural, one{comment} few{items} other{comments}} successfully {count, plural, one{deleted} few{deleted} other{deleted}}.',
                                [
                                    'count' => $deleted
                                ]
                            )
                        );
                    } else {
                        // Log activity
                        $this->module->logActivity(
                            'An error occurred while deleting a comment(s).',
                            $this->uniqueId . ":" . $this->action->id,
                            'danger',
                            1
                        );

                        Yii::$app->getSession()->setFlash(
                            'danger',
                            Yii::t(
                                'app/modules/comments',
                                'An error occurred while deleting a comment(s).'
                            )
                        );
                    }
                }
            }
        }

        return $this->redirect(['comments/list', 'context' => $context, 'target' => $target]);
    }

    /**
     * Finds the Comments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Comments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Comments::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app/modules/comments', 'The requested page does not exist.'));
    }
}
