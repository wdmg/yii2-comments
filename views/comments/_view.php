<?php

use wdmg\helpers\StringHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $model wdmg\comments\models\Comments */

\yii\web\YiiAsset::register($this);

?>
<div class="comments-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'parent_id',
            [
                'attribute' => 'comment',
                'format' => 'html',
                'value' => function($data) {
                    if (!is_null($data->parent_id))
                        return Html::tag('span', "↳", ['class' => "text-muted"]) .
                            "&nbsp;" . Html::tag('em', $data->comment);
                    else
                        return $data->comment;
                }
            ],
            'context',
            'target',
            [
                'attribute' => 'user_id',
                'label' => Yii::t('app/modules/comments','Author'),
                'format' => 'html',
                'value' => function($data) {

                    $output = "";
                    if ($user = $data->user) {
                        $output .= Yii::t('app/modules/comments','User: {username}', [
                            'username' => Html::a($user->username, ['/users/users/view', 'id' => $user->id], [
                                'target' => '_blank',
                                'data-pjax' => 0
                            ])
                        ]);
                    } elseif ($data->user_id) {
                        $output .= $data->user_id;
                    } elseif ($data->name && $data->email) {
                        $output .= Yii::t('app/modules/comments','Guest: {name}', [
                            'name' => Html::mailto($data->name, Url::to($data->email), [
                                'target' => '_blank',
                                'data-pjax' => 0
                            ])
                        ]);
                    }

                    if (!empty($output))
                        $output .= ", ";

                    $output .= Yii::$app->formatter->format($data->updated_at, 'datetime');
                    return $output;
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function($data) {
                    if ($data->status == $data::COMMENT_STATUS_REJECTED)
                        return Html::tag('span', Yii::t('app/modules/comments', 'Rejected'), [
                            'class' => 'label label-danger'
                        ]);
                    elseif ($data->status == $data::COMMENT_STATUS_DELETED)
                        return Html::tag('span', Yii::t('app/modules/comments', 'Deleted'), [
                            'class' => 'label label-default'
                        ]);
                    elseif ($data->status == $data::COMMENT_STATUS_AWAITING)
                        return Html::tag('span', Yii::t('app/modules/comments', 'On moderation'), [
                            'class' => 'label label-warning'
                        ]);
                    elseif ($data->status == $data::COMMENT_STATUS_PUBLISHED)
                        return Html::tag('span', Yii::t('app/modules/comments', 'Published'), [
                            'class' => 'label label-success'
                        ]);

                    return $data->status;
                }
            ],
            'session',
            'created_at:datetime',
            'updated_at:datetime',

        ],
    ]) ?>
    <div class="modal-footer">
        <?= Html::a(Yii::t('app/modules/comments', 'Close'), "#", [
            'class' => 'btn btn-default pull-left',
            'data-dismiss' => 'modal'
        ]); ?>
    </div>
</div>
