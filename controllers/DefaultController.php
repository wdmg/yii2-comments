<?php

namespace wdmg\comments\controllers;

use wdmg\helpers\StringHelper;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
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
    public function behaviors() {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST', 'GET'],
                    'edit' => ['POST', 'GET'],
                    'delete' => ['GET'],
                    'reply' => ['GET'],
                    'abuse' => ['GET'],
                ]
            ]
        ];

        return $behaviors;
    }

    /**
     * Adds a new comment to the frontend.
     *
     * @return \yii\web\Response
     */
    public function actionCreate() {

        $model = new Comments();
        $model->setScenario($model::COMMENT_SCENARIO_CREATE);

        if ($id = Yii::$app->request->post('Comments')['id'])
            return $this->actionEdit($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                if ($model->onModerateStatus())
                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t(
                            'app/modules/comments',
                            'OK! Your comment successfully sended but awaiting to moderation.'
                        )
                    );
                else
                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t(
                            'app/modules/comments',
                            'OK! Your comment successfully published.'
                        )
                    );

            } else {
                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t(
                        'app/modules/comments',
                        'An error occurred while add your comment.'
                    )
                );
            }
        }

        return $this->goBack(
            (!empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null)
        );
    }

    /**
     * Changes the comment to the frontend (authors only).
     * @param null $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionEdit($id = null) {

        $model = self::findModel($id, true);
        $model->setScenario($model::COMMENT_SCENARIO_UPDATE);
        $editTimeout = $this->module->editCommentTimeout;
        if ($editTimeout && !(intval($editTimeout) >= (time() - strtotime($model->updated_at)))) {
            Yii::$app->getSession()->setFlash(
                'warning',
                Yii::t(
                    'app/modules/comments',
                    'Unfortunately, you can no longer edit this comment.'
                )
            );
        } else {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($model->update()) {
                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t(
                            'app/modules/comments',
                            'OK! Your comment successfully updated.'
                        )
                    );
                } else {
                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t(
                            'app/modules/comments',
                            'An error occurred while updating your comment.'
                        )
                    );
                }
            }
        }

        return $this->goBack(
            (!empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null)
        );
    }

    /**
     *
     * @param null $id
     * @return \yii\web\Response
     */
    public function actionReply($id = null) {
        return $this->goBack(
            (!empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null)
        );
    }

    /**
     * Deletes the comment in the frontend (authors only).
     *
     * @param null $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id = null) {
        $model = self::findModel($id, true);
        $deleteTimeout = $this->module->deleteCommentTimeout;
        if ($deleteTimeout && !(intval($deleteTimeout) >= (time() - strtotime($model->updated_at)))) {
            Yii::$app->getSession()->setFlash(
                'warning',
                Yii::t(
                    'app/modules/comments',
                    'Unfortunately, you can no longer delete this comment.'
                )
            );
        } else {
            $model->status = $model::COMMENT_STATUS_DELETED;
            if ($model->update()) {
                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t(
                        'app/modules/comments',
                        'OK! Your comment successfully deleted.'
                    )
                );
            } else {
                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t(
                        'app/modules/comments',
                        'An error occurred while deleting a your comment.'
                    )
                );
            }
        }

        return $this->goBack(
            (!empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null)
        );
    }

    /**
     * Sends a complaint about the comment to the frontend.
     *
     * @param null $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionAbuse($id = null) {
        $model = self::findModel($id);
        $url = Url::to(['comments/list', 'id' => $model->id, 'context' => $model->context, 'target' => $model->target], true);
        if ($user = Yii::$app->getUser()) {
            if (!$user->isGuest && ($identity = $user->getIdentity())) {
                $sender = Yii::t('app/modules/comments', 'User {username}', [
                    'username' => Html::a($identity->username, ['/users/users/view', 'id' => $identity->getId()], [
                        'target' => '_blank',
                        'data-pjax' => 0
                    ])
                ]);
            } else {
                $sender = Yii::t('app/modules/comments','Guest');
            }
        } else {
            $sender = Yii::t('app/modules/comments','Guest');
        }

        $comment = StringHelper::truncateWords(StringHelper::stripTags($model->comment, "", " "),12,'…');

        if ($user = $model->user) {
            $author_link = Html::a($user->username, ['/users/users/view', 'id' => $user->id], [
                'target' => '_blank',
                'data-pjax' => 0
            ]);
            $author = Yii::t('app/modules/comments','user {username}', [
                'username' => $author_link
            ]);
        } else {
            $author_link = Html::mailto($model->name, Url::to($model->email), [
                'target' => '_blank',
                'data-pjax' => 0
            ]);
            $author = Yii::t('app/modules/comments','guest {name}', [
                'name' => $author_link
            ]);
        }

        $message = Yii::$app->mailer->compose();
        $message->setFrom(Yii::$app->params['senderEmail']);
        $message->setTo(Yii::$app->params['adminEmail'])
            ->setSubject(Yii::t(
                'app/modules/comments',
                'Abuse to comment'
            ))->setTextBody(Yii::t(
                'app/modules/comments',
                'Hi! {sender} has submitted a complaint about comment `{comment}` by {author}. You can moderate the comment here: {url}', [
                    'sender' => $sender,
                    'comment' => $comment,
                    'author' => $author_link,
                    'url' => $url,
                ]
            ))->setHtmlBody(Yii::t(
                'app/modules/comments',
                'Hi! {sender} has submitted a complaint about comment `{comment}` by {author}. You can moderate the comment here: {url}', [
                    'sender' => $sender,
                    'comment' => $comment,
                    'author' => $author,
                    'url' => Html::a($url, $url, [
                        'target' => '_blank',
                        'data-pjax' => 0
                    ]),
                ]
            ));

        if ($message->send()) {

            // Log activity
            $this->module->logActivity(
                "Submitted a complaint about comment `$comment` by $author_link. Link to comment: " . Html::a($url, $url, [
                    'target' => '_blank',
                    'data-pjax' => 0
                ]),
                $this->uniqueId . ":" . $this->action->id,
                'warning',
                1
            );

            Yii::$app->getSession()->setFlash(
                'success',
                Yii::t(
                    'app/modules/comments',
                    'OK! Your abuse to comment successfully sended.'
                )
            );
        } else {

            // Log activity
            $this->module->logActivity(
                "An error occurred while sending complaint about comment `$comment` by $author_link. Link to comment: " . Html::a($url, $url, [
                    'target' => '_blank',
                    'data-pjax' => 0
                ]),
                $this->uniqueId . ":" . $this->action->id,
                'danger',
                1
            );

            Yii::$app->getSession()->setFlash(
                'danger',
                Yii::t(
                    'app/modules/comments',
                    'An error occurred while sending your abuse to comment.'
                )
            );
        }

        return $this->goBack(
            (!empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null)
        );
    }

    /**
     * Finds the Comments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param $id
     * @param bool $asAuthor, check by author of comment (by $user->id or $session->id)
     * @return array|Comments|\yii\db\ActiveRecord|null
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    protected function findModel($id, $asAuthor = false)
    {

        if ($asAuthor && ($user = Yii::$app->getUser())) {
            if (!$user->isGuest && ($identity = $user->getIdentity())) {
                $user_id = $identity->getId();
                if (($model = Comments::find()->where(['id' => $id, 'user_id' => $user_id])->one()) !== null) {
                    return $model;
                }
            } else {
                $session = Yii::$app->getSession()->getId();
                if (($model = Comments::find()->where(['id' => $id, 'user_id' => null, 'session' => $session])->one()) !== null) {
                    return $model;
                }
            }
        } else {
            if (($model = Comments::findOne($id)) !== null) {
                return $model;
            }
        }

        throw new NotFoundHttpException(Yii::t('app/modules/comments', 'The requested comment does not exist.'));
    }
}
