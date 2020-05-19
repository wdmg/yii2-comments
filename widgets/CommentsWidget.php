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
    public $formOptions;

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
        'editTimeout' => 3600, // time in seconds
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
            $this->listActions['edit'] = Url::to([$module->defaultController . '/edit']);

        if (!isset($this->listActions['delete']))
            $this->listActions['delete'] = Url::to([$module->defaultController . '/delete']);

        if (!isset($this->listActions['abuse']))
            $this->listActions['abuse'] = Url::to([$module->defaultController . '/abuse']);

        if (!isset($this->listActions['reply']))
            $this->listActions['reply'] = '#reply';

        if (!isset($this->formView))
            $this->formView = $module->defaultFormView;

        if (!isset($this->formAction))
            $this->formAction = Url::to([$module->defaultController . '/create']);

        if ($comments = Yii::$app->comments->get($this->context, $this->target))
            $this->_comments = $comments;

        $this->_model = Yii::$app->comments->getModel($this->context, $this->target, true);

        parent::init();
    }

    public function run()
    {
        $output = '';

        if (!is_null($this->_comments) && $this->listView !== false) {
            $output .= $this->render($this->listView, [
                'id' => $this->listId,
                'form_id' => $this->formId,
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
                'options' => (is_array($this->formOptions)) ? $this->formOptions : null,
                'model' => $this->_model,
                'bundle' => $this->_bundle
            ]);
        }

        return $output;
    }

}