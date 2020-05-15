<?php

namespace wdmg\comments\widgets;

use wdmg\helpers\ArrayHelper;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use wdmg\comments\CommentsAsset;
use yii\i18n\Formatter;

class CommentsWidget  extends Widget
{
    public $comments;
    public $maxLevels;
    public $options = [
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

        'userPhotoAlign' => 'left', // left/right

        'userPhotoOptions' => [
            'class' => 'media-object img-circle img-thumbnail size-64x64'
        ],

        'userLinkOptions' => ['class' => 'user-link'],
    ];

    private $_bundle;

    public function init()
    {
        $view = $this->getView();
        $this->_bundle = CommentsAsset::register($view);
        parent::init();
    }

    public function run()
    {
        $output = $this->buildCommentsTree($this->comments, $this->maxLevels);
        echo $output;
    }


    private function buildCommentsTree($comments = [], $maxLevels = 2) {

        $output = '';
        $isRootLevel = false;
        $isMaxLevel = false;

        if (is_string($this->options['listRootTag']))
            $listRootTag = $this->options['listRootTag'];
        else
            $listRootTag = 'ul';

        if (is_array($this->options['listRootOptions']))
            $listRootOptions = $this->options['listRootOptions'];


        if (is_string($this->options['itemRootTag']))
            $itemRootTag = $this->options['itemRootTag'];
        else
            $itemRootTag = 'li';

        if (is_array($this->options['itemRootOptions']))
            $itemRootOptions = $this->options['itemRootOptions'];


        if (is_string($this->options['itemReplyTag']))
            $itemReplyTag = $this->options['itemReplyTag'];
        else
            $itemReplyTag = 'div';

        if (is_array($this->options['itemReplyOptions']))
            $itemReplyOptions = $this->options['itemReplyOptions'];


        if (is_string($this->options['userPhotoAlign']))
            $userPhotoAlign = $this->options['userPhotoAlign'];

        if (is_array($this->options['userPhotoOptions']))
            $userPhotoOptions = $this->options['userPhotoOptions'];

        if (is_array($this->options['userLinkOptions']))
            $userLinkOptions = $this->options['userLinkOptions'];

        foreach($comments as $comment) {

            $item = '';

            if (isset($comment['_level'])) {

                if (intval($comment['_level']) == 1)
                    $isRootLevel = true;

                if (intval($comment['_level']) > intval($maxLevels))
                    $isMaxLevel = true;

            }

            if (isset($comment['photo']))
                $photo = Html::img(
                    $comment['photo'],
                    ArrayHelper::merge($userPhotoOptions, [
                        'alt' => $comment['name'],
                    ])
                );
            else
                $photo = Html::img("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+PCEtLQpTb3VyY2UgVVJMOiBob2xkZXIuanMvNjR4NjQKQ3JlYXRlZCB3aXRoIEhvbGRlci5qcyAyLjYuMC4KTGVhcm4gbW9yZSBhdCBodHRwOi8vaG9sZGVyanMuY29tCihjKSAyMDEyLTIwMTUgSXZhbiBNYWxvcGluc2t5IC0gaHR0cDovL2ltc2t5LmNvCi0tPjxkZWZzPjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+PCFbQ0RBVEFbI2hvbGRlcl8xNzIxODBlNjBlNSB0ZXh0IHsgZmlsbDojQUFBQUFBO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1mYW1pbHk6QXJpYWwsIEhlbHZldGljYSwgT3BlbiBTYW5zLCBzYW5zLXNlcmlmLCBtb25vc3BhY2U7Zm9udC1zaXplOjEwcHQgfSBdXT48L3N0eWxlPjwvZGVmcz48ZyBpZD0iaG9sZGVyXzE3MjE4MGU2MGU1Ij48cmVjdCB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIGZpbGw9IiNFRUVFRUUiLz48Zz48dGV4dCB4PSIxMy45MjE4NzUiIHk9IjM2LjM2NDA2MjUiPjY0eDY0PC90ZXh0PjwvZz48L2c+PC9zdmc+",
                    ArrayHelper::merge($userPhotoOptions, [
                        'alt' => $comment['name'],
                    ])
                );

            if (isset($comment['link']))
                $link = Html::a($photo, $comment['link'], $userLinkOptions);
            else
                $link = $photo;

            if ($userPhotoAlign == 'left') {
                $item .= Html::tag('div', $link, ['class' => 'media-left']);
            }

            $item .= '<div class="media-body">';

            $item .= '<div class="media-content">';

            $item .= '<h5 class="media-heading">';
            $item .= '#' . $comment['id'] . ' <b>' . $comment['name'] . '</b> say`s:';

            $item .= Html::tag('small', Html::tag('i', '&nbsp;', [
                    'class' => "fa fa-clock fa-fw"
                ]) . \Yii::$app->formatter->asDatetime($comment['updated_at'], 'dd MMMM в HH:mm'), [
                'class' => "pull-right text-muted"
            ]);

            $item .= '</h5>'; // .media-heading

            $item .= Html::tag('p', $comment['comment']);

            $item .= '</div>'; // .media-content


            $item .= '<div class="media-footer">';
            if (intval($comment['user_id']) == Yii::$app->getUser()->getId()) {
                $item .= '<a href="#" class="btn btn-sm btn-link pull-right">Edit</a>';
            }
            if (intval($comment['user_id']) !== Yii::$app->getUser()->getId()) {
                $item .= '<a href="#" class="btn btn-sm btn-link pull-left">Abuse</a>';

                $item .= '<a href="#" class="btn btn-sm btn-link pull-right">Reply</a>';

                /*
                $output .= '<a href="#" class="btn btn-sm btn-link pull-right">Dislike</a>';
                $output .= '<a href="#" class="btn btn-sm btn-link pull-right">Like</a>';
                */

            }
            $item .= '</div>'; // .media-footer


            if (isset($comment['items'])) {
                if (is_array($comment['items'])) {
                    $item .= $this->buildCommentsTree($comment['items'], $maxLevels);
                }
            }
            $item .= '</div>'; // .media-body

            if ($userPhotoAlign == 'right') {
                $item .= Html::tag('div', $link, ['class' => 'media-right']);
            }

            if ($isRootLevel) {
                $output .= Html::tag($itemRootTag, $item, ArrayHelper::merge($itemRootOptions, [
                    'data' => [
                        'key' => $comment['id']
                    ],
                ]));
            } else {
                $output .= Html::tag($itemReplyTag, $item, ArrayHelper::merge($itemReplyOptions, [
                    'data' => [
                        'key' => $comment['id']
                    ],
                ]));
            }
        }

        if (!$isMaxLevel) {
            if ($isRootLevel)
                return Html::tag($listRootTag, $output, $listRootOptions);
            else
                return $output;
        } else {
            return $output;
        }
    }

}