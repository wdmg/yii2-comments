<?php

use wdmg\helpers\StringHelper;
use wdmg\widgets\SelectInput;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel wdmg\comments\models\CommentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/modules/comments','Context `{context}`, target `{target}`', [
    'context' => $context,
    'target' => $target,
]);
$this->params['breadcrumbs'][] = ['label' => $this->context->module->name, 'url' => ['comments/index']];
$this->params['breadcrumbs'][] = Yii::t('app/modules/comments','Comments list');

?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="comments-index">
    <?php Pjax::begin([
        'id' => "pageContainer"
    ]); ?>
    <?= GridView::widget([
        'id' => "commentsList",
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions' => function($model) {
                    return [
                        'value' => $model->id
                    ];
                }
            ],
            [
                'attribute' => 'comment',
                'format' => 'html',
                'value' => function($data) {
                    $comment = StringHelper::truncateWords(StringHelper::stripTags($data->comment, "", " "),12,'…');
                    if (!is_null($data->parent_id))
                        return Html::tag('span', "↳", ['class' => "text-muted"]) .
                            "&nbsp;" . Html::tag('em', $comment);
                    else
                        return $comment;
                }
            ],
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
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'status',
                    'items' => $searchModel->getStatusesList(true),
                    'options' => [
                        'id' => 'comments-status',
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
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
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app/modules/media','Actions'),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'buttons'=> [
                    'delete' => function($url, $data, $key) {
                        return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-trash"]), [
                            'comments/delete',
                            'id' => $key,
                            'context' => $data->context,
                            'target' => $data->target
                        ], [
                            'data-method' => 'POST',
                            'data-confirm' => Yii::t('app/modules/comments', 'Are you sure you want to delete the comment? All replies to this comment will also be deleted.')
                        ]);
                    },
                    'view' => function($url, $data, $key) {
                        return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-eye-open"]), [
                            'comments/view',
                            'id' => $key,
                            'context' => $data->context,
                            'target' => $data->target
                        ], [
                            'class' => "comments-view-link"
                        ]);
                    },
                    'update' => function($url, $data, $key) {
                        return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-edit"]), [
                            'comments/update',
                            'id' => $key
                        ], [
                            'class' => "comments-update-link"
                        ]);
                    }
                ]
            ]
        ],
        'pager' => [
            'options' => [
                'class' => 'pagination',
            ],
            'maxButtonCount' => 5,
            'activePageCssClass' => 'active',
            'prevPageCssClass' => 'prev',
            'nextPageCssClass' => 'next',
            'firstPageCssClass' => 'first',
            'lastPageCssClass' => 'last',
            'firstPageLabel' => Yii::t('app/modules/comments', 'First page'),
            'lastPageLabel'  => Yii::t('app/modules/comments', 'Last page'),
            'prevPageLabel'  => Yii::t('app/modules/comments', '&larr; Prev page'),
            'nextPageLabel'  => Yii::t('app/modules/comments', 'Next page &rarr;')
        ],
    ]); ?>
    <hr/>
    <div class="form-group">
        <?= Html::a(Yii::t('app/modules/comments', '&larr; Back to list'), ['comments/index'], ['class' => 'btn btn-default']) ?>&nbsp;
        <?= Html::button(Yii::t('app/modules/comments', 'Select action') . ' <span class="caret"></span>', [
            'id' => 'batchSelectAction',
            'class' => 'btn btn-default dropdown-toggle',
            'data-toggle' => 'dropdown',
            'aria-haspopup' => 'true',
            'aria-expanded' => 'false',
            'disabled' => 'disabled',
            'data-pjax' => '0'
        ]) ?>
        <ul class="dropdown-menu">
                <?php
                if ($statuses = $searchModel->getStatusesList(false)) {
                    foreach ($statuses as $key => $name) {
                        echo "<li>" . Html::a(Yii::t('app/modules/comments', 'Change status to: {name}', [
                                'name' => $name
                            ]), [
                                'comments/batch',
                                'action' => 'change',
                                'attribute' => 'status',
                                'value' => $key,
                                'context' => $context,
                                'target' => $target
                            ], [
                                'id' => 'changeStatusSelected',
                                'data-method' => 'POST',
                                'data-pjax' => '0'
                            ]) . "</li>";
                    }
                }
                ?>
                <li role="separator" class="divider"></li>
                <li>
                    <?= Html::a(Yii::t('app/modules/comments', 'Delete selected'), [
                        'comments/batch',
                        'action' => 'delete',
                        'context' => $context,
                        'target' => $target
                    ], [
                        'id' => 'batchDeleteSelected',
                        'class' => 'bg-danger text-danger',
                        'data-pjax' => '0'
                    ]) ?>
                </li>
            </ul>
    </div>
    <?php Pjax::end(); ?>
</div>

<?php $this->registerJs(<<< JS
    $('body').delegate('#commentsList input[type="checkbox"]', 'click', function(event) {
        setTimeout(function() {
            var selected = $('#commentsList').yiiGridView('getSelectedRows');
        if (selected.length) {
            $('#batchSelectAction').removeAttr('disabled');
        } else {
            $('#batchSelectAction').attr('disabled', 'disabled');
        }
        }, 300);
    });
    $('body').delegate('#changeStatusSelected, #batchDeleteSelected', 'click', function(event) {
        event.preventDefault();
        var url = $(event.target).attr('href');
        var selected = $('#commentsList').yiiGridView('getSelectedRows');
        if (selected.length) {
            $.post({
                url: url,
                data: {selected: selected},
                success: function(data) {
                    $.pjax({
                        container: "#pageContainer"
                    });
                },
                error:function(erorr, responseText, code) {
                    window.location.reload();
                }
            });
        }
    });
    $('body').delegate('.comments-view-link', 'click', function(event) {
        event.preventDefault();
        $.get(
            $(this).attr('href'),
            function (data) {
                $('#commentsView .modal-body').html($(data).remove('.modal-footer'));
                if ($(data).find('.modal-footer').length > 0) {
                    $('#commentsView').find('.modal-footer').remove();
                    $('#commentsView .modal-content').append($(data).find('.modal-footer'));
                }
                $('#commentsView').modal();
            }  
        );
    });
    $('body').delegate('.comments-update-link', 'click', function(event) {
        event.preventDefault();
        $.get(
            $(this).attr('href'),
            function (data) {
                $('#commentsUpdate .modal-body').html($(data).remove('.modal-footer'));
                if ($(data).find('.modal-footer').length > 0) {
                    $('#commentsUpdate').find('.modal-footer').remove();
                    $('#commentsUpdate .modal-content').append($(data).find('.modal-footer'));
                }
                $('#commentsUpdate').modal();
            }  
        );
    });
JS
); ?>

<?php Modal::begin([
    'id' => 'commentsView',
    'header' => '<h4 class="modal-title">'.Yii::t('app/modules/comments', 'View comment').'</h4>',
]); ?>
<?php echo $this->render('_view', ['model' => $searchModel]); ?>
<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'commentsUpdate',
    'header' => '<h4 class="modal-title">'.Yii::t('app/modules/comments', 'Update comment').'</h4>',
]); ?>
<?php echo $this->render('_form', ['model' => $searchModel]); ?>
<?php Modal::end(); ?>

<?php echo $this->render('../_debug'); ?>