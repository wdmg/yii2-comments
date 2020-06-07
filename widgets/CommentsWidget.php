<?php

namespace wdmg\comments\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use \yii\helpers\Url;
use wdmg\helpers\ArrayHelper;
use wdmg\comments\CommentsAsset;

class CommentsWidget  extends Widget
{

    public $context;
    public $target;

    public $formId;
    public $formView;
    public $formAction;
    public $formOptions = [
        'headerTag' => null,
        'headerLabel' => null,
        'headerOptions' => [
            'class' => "page-header"
        ],
        'formOptions' => null,
        'nameLabel' => null,
        'nameOptions' => [
            'labelOptions' => [
                'class' => 'control-label col-xs-12 col-xs-6 col-md-3',
            ],
            'template' => '{label}<div class="col-xs-12 col-sm-6 col-md-9">{input}</div><div class="col-xs-12 col-sm-6 col-sm-offset-6 col-md-9 col-md-offset-3">{error}</div>',
        ],
        'emailLabel' => null,
        'emailOptions' => [
            'labelOptions' => [
                'class' => 'control-label col-xs-12 col-xs-6 col-md-3',
            ],
            'template' => '{label}<div class="col-xs-12 col-sm-6 col-md-9">{input}</div><div class="col-xs-12 col-sm-6 col-sm-offset-6 col-md-9 col-md-offset-3">{error}</div>',
        ],
        'commentLabel' => null,
        'commentOptions' => [],
        'submitOptions' => [
            'class' => "btn btn-primary"
        ]
    ];
    public $formTemplate = '<h4 class="page-header">{header}</h4><div class="row">{name}{email}</div>{comment}<div class="form-group">{submit}</div>';

    public $listId;
    public $listView;
    public $listActions;
    public $listOptions = [
        'listRootTag' => 'ul',
        'listRootOptions' => [
            'class' => 'media-list comments-list'
        ],
        'itemRootTag' => 'li',
        'itemRootOptions' => [
            'class' => 'media comment-item'
        ],
        'itemReplyTag' => 'div',
        'itemReplyOptions' => [
            'class' => 'media comment-reply'
        ],
        'editTimeout' => null,
        'deleteTimeout' => null,
        'userPhotoSize' => 64, // 64/96/128px
        'useGravatar' => false,
        'userPhotoAlign' => 'left', // left/right
        'userPhotoOptions' => [
            'class' => 'media-object img-circle img-thumbnail'
        ],
        'userLinkOptions' => ['class' => 'user-link'],
    ];

    private $_bundle;
    private $_model;
    private $_comments;

    public function init()
    {
        $view = $this->getView();
        $this->_bundle = CommentsAsset::register($view);
        $module = Yii::$app->comments->getModule();

        if (!isset($this->formId) && ($id = $this->getId()))
            $this->formId = 'commentsForm-' . $id;

        if (!isset($this->listId) && ($id = $this->getId()))
            $this->listId = 'commentsList-' . $id;

        if (!isset($this->listView))
            $this->listView = $module->defaultListView;

        if (!isset($this->listActions['edit']))
            $this->listActions['edit'] = Url::to([$module->baseRoute . '/edit']);

        if (!isset($this->listActions['delete']))
            $this->listActions['delete'] = Url::to([$module->baseRoute . '/delete']);

        if (!isset($this->listActions['abuse']))
            $this->listActions['abuse'] = Url::to([$module->baseRoute . '/abuse']);

        if (!isset($this->listActions['reply']))
            $this->listActions['reply'] = '#reply';

        if (!isset($this->formView))
            $this->formView = $module->defaultFormView;

        if (!isset($this->formAction))
            $this->formAction = Url::to([$module->baseRoute . '/create']);

        if ($comments = Yii::$app->comments->get($this->context, $this->target))
            $this->_comments = $comments;

        if (!isset($listOptions['editTimeout']) && isset($module->editCommentTimeout))
            $listOptions['editTimeout'] = $module->editCommentTimeout;

        if (!isset($listOptions['deleteTimeout']) && isset($module->deleteCommentTimeout))
            $listOptions['deleteTimeout'] = $module->deleteCommentTimeout;

        $this->_model = Yii::$app->comments->getModel($this->context, $this->target, true);
        parent::init();
    }

    public function run()
    {
        $output = '';
        $returnUrl = Yii::$app->getRequest()->absoluteUrl;
        Yii::$app->getUser()->setReturnUrl($returnUrl);

        if (!is_null($this->_comments) && $this->listView !== false) {
            $output .= $this->render($this->listView, [
                'id' => $this->listId,
                'form_id' => $this->formId,
                'context' => $this->context,
                'target' => $this->target,
                'count' => $this->_comments['count'],
                'actions' => $this->listActions,
                'comments' => $this->_comments['items'],
                'options' => (is_array($this->listOptions)) ? $this->listOptions : null,
                'bundle' => $this->_bundle
            ]);
        }

        if (!is_null($this->_model) && $this->formView !== false) {
            $output .= $this->render($this->formView, [
                'id' => $this->formId,
                'action' => $this->formAction,
                'context' => $this->context,
                'target' => $this->target,
                'options' => (is_array($this->formOptions)) ? $this->formOptions : null,
                'template' => (!is_null($this->formTemplate)) ? $this->formTemplate : null,
                'model' => $this->_model,
                'bundle' => $this->_bundle
            ]);
        }

        return $output;
    }

}