<?php

use wdmg\helpers\StringHelper;
use wdmg\widgets\SelectInput;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel wdmg\comments\models\CommentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $this->context->module->name;
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="comments-index">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            /*'parent_id',*/
            [
                'attribute' => 'context',
                'format' => 'html',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'context',
                    'items' => $searchModel->getContextsList(true),
                    'options' => [
                        'id' => 'comments-contexts',
                        'class' => 'form-control'
                    ]
                ]),
            ],
            [
                'attribute' => 'target',
                'format' => 'html',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'target',
                    'items' => $searchModel->getTargetsList(true),
                    'options' => [
                        'id' => 'comments-targets',
                        'class' => 'form-control'
                    ]
                ]),
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
                'attribute' => 'count',
                'format' => 'raw',
                'label' => Yii::t('app/modules/comments', 'Manage'),
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'range',
                    'items' => $searchModel->getCommentsRangeList(true),
                    'options' => [
                        'id' => 'comments-range',
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

                    $counters = [];
                    $counts = $data->getCounts();

                    $published = 0;
                    if (isset($counts[$data::COMMENT_STATUS_PUBLISHED]))
                        $published = $counts[$data::COMMENT_STATUS_PUBLISHED];

                    $counters[] = Html::a($published, [
                        'comments/list',
                        'status' => $data::COMMENT_STATUS_PUBLISHED,
                        'context' => $data->context,
                        'target' => $data->target,
                    ], [
                        'class' => "label label-success",
                        'title' => 'Comments has been published',
                        'disabled' => ($published) ? false : true,
                        'data' => [
                            'toggle' => "tooltip",
                            'pjax' => 0
                        ]
                    ]);


                    $awaiting = 0;
                    if (isset($counts[$data::COMMENT_STATUS_AWAITING]))
                        $awaiting = $counts[$data::COMMENT_STATUS_AWAITING];

                    $counters[] = Html::a($awaiting, [
                        'comments/list',
                        'status' => $data::COMMENT_STATUS_AWAITING,
                        'context' => $data->context,
                        'target' => $data->target,
                    ], [
                        'class' => "label label-warning",
                        'title' => 'Comments has awaiting moderation',
                        'disabled' => ($awaiting) ? false : true,
                        'data' => [
                            'toggle' => "tooltip",
                            'pjax' => 0
                        ]
                    ]);


                    $deleted = 0;
                    if (isset($counts[$data::COMMENT_STATUS_DELETED]))
                        $deleted = $counts[$data::COMMENT_STATUS_DELETED];

                    $counters[] = Html::a($deleted, [
                        'comments/list',
                        'status' => $data::COMMENT_STATUS_DELETED,
                        'context' => $data->context,
                        'target' => $data->target,
                    ], [
                        'class' => "label label-default",
                        'title' => 'Comments has been deleted',
                        'disabled' => ($deleted) ? false : true,
                        'data' => [
                            'toggle' => "tooltip",
                            'pjax' => 0
                        ]
                    ]);


                    $rejected = 0;
                    if (isset($counts[$data::COMMENT_STATUS_REJECTED]))
                        $rejected = $counts[$data::COMMENT_STATUS_REJECTED];

                    $counters[] = Html::a($rejected, [
                        'comments/list',
                        'status' => $data::COMMENT_STATUS_REJECTED,
                        'context' => $data->context,
                        'target' => $data->target,
                    ], [
                        'class' => "label label-danger",
                        'title' => 'Comments has been rejected',
                        'disabled' => ($rejected) ? false : true,
                        'data' => [
                            'toggle' => "tooltip",
                            'pjax' => 0
                        ]
                    ]);

                    if ($data->count)
                        $counters[] = Html::a($data->count, [
                            'comments/list',
                            'context' => $data->context,
                            'target' => $data->target
                        ], [
                            'class' => "label label-info",
                            'title' => 'All comments',
                            'disabled' => ($data->count) ? false : true,
                            'data' => [
                                'toggle' => "tooltip",
                                'pjax' => 0
                            ]
                        ]);

                    return implode(" ", $counters);
                }
            ]
        ],
        'pager' => [
            'options' => [
                'class' => 'pagination',
            ],
            'maxButtonCount' => 5,
            'activePageCssClass' => 'active',
            'prevPageCssClass' => '',
            'nextPageCssClass' => '',
            'firstPageCssClass' => 'previous',
            'lastPageCssClass' => 'next',
            'firstPageLabel' => Yii::t('app/modules/comments', 'First page'),
            'lastPageLabel'  => Yii::t('app/modules/comments', 'Last page'),
            'prevPageLabel'  => Yii::t('app/modules/comments', '&larr; Prev page'),
            'nextPageLabel'  => Yii::t('app/modules/comments', 'Next page &rarr;')
        ],
    ]); ?>
    <hr/>
    <?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>