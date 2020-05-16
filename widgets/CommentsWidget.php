<?php

namespace wdmg\comments\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\i18n\Formatter;
use \yii\helpers\Url;
use wdmg\helpers\ArrayHelper;
use wdmg\comments\CommentsAsset;

class CommentsWidget  extends Widget
{
    public $comments;
    public $editTimeout = 300;
    public $useGravatar = false;
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

        'userPhotoSize' => 64, // 32px
        'userPhotoAlign' => 'left', // left/right

        'userPhotoOptions' => [
            'class' => 'media-object img-circle img-thumbnail'
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
        $output = $this->buildCommentsTree($this->comments);
        echo $output;
    }

    private function buildCommentsTree($comments = []) {

        $output = '';
        $userId = Yii::$app->getUser()->getId();

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


        if (is_numeric($this->options['userPhotoSize']))
            $userPhotoSize = $this->options['userPhotoSize'];

        if (is_string($this->options['userPhotoAlign']))
            $userPhotoAlign = $this->options['userPhotoAlign'];

        if (is_array($this->options['userPhotoOptions']))
            $userPhotoOptions = $this->options['userPhotoOptions'];


        if (is_array($this->options['userLinkOptions']))
            $userLinkOptions = $this->options['userLinkOptions'];

        foreach($comments as $comment) {

            $item = '';
            $isRootLevel = false;

            if (isset($comment['_level'])) {

                if (intval($comment['_level']) == 1)
                    $isRootLevel = true;

            }

            if ($userPhotoSize == 32) {
                $userDefaultPhoto = $this->_bundle->baseUrl . "/images/user-default-32.png";
                $userPhotoSizeClass = ["size-32x32"];
            } else if ($userPhotoSize == 96) {
                $userDefaultPhoto = $this->_bundle->baseUrl . "/images/user-default-96.png";
                $userPhotoSizeClass = ["size-96x96"];
            } else if ($userPhotoSize == 128) {
                $userDefaultPhoto = $this->_bundle->baseUrl . "/images/user-default-128.png";
                $userPhotoSizeClass = ["size-128x128"];
            } else {
                $userDefaultPhoto = $this->_bundle->baseUrl . "/images/user-default-64.png";
                $userPhotoSizeClass = ["size-64x64"];
            }

            if ($this->useGravatar && isset($comment['email']) && !isset($comment['photo'])) {
                $hash = md5(strtolower(trim($comment['email'])));
                $defaultPhoto = urlencode(Url::to($userDefaultPhoto, true));
                $comment['photo'] = "https://www.gravatar.com/avatar/" . $hash . "?s=" . $userPhotoSize. "&d=" . $defaultPhoto;
            }

            if (isset($comment['photo']))
                $photo = Html::img(
                    $comment['photo'],
                    ArrayHelper::merge($userPhotoOptions, $userPhotoSizeClass, [
                        'alt' => $comment['name'],
                    ])
                );
            else
                $photo = Html::img($userDefaultPhoto,
                    ArrayHelper::merge($userPhotoOptions, $userPhotoSizeClass, [
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
            if ($this->editTimeout && !is_null($userId)) {
                if (
                    ((intval($comment['user_id']) == $userId) && !is_null($userId)) ||
                    ($comment['user_id'] == \Yii::$app->session->getId())
                ) {
                    $item .= Html::a('Delete', '#delete', [
                        'class' => 'btn btn-sm btn-link pull-right',
                        'data' => [
                            'key' => $comment['id']
                        ]
                    ]);

                    if (intval($this->editTimeout) >= (time() - strtotime($comment['updated_at']))) {
                        $item .= Html::a('Edit', '#edit', [
                            'class' => 'btn btn-sm btn-link pull-right',
                            'data' => [
                                'key' => $comment['id']
                            ]
                        ]);
                    }
                }
            }
            if (intval($comment['user_id']) !== $userId) {
                $item .= Html::a('Abuse', '#abuse', [
                    'class' => 'btn btn-sm btn-link pull-left',
                    'data' => [
                        'key' => $comment['id']
                    ]
                ]);

                $item .= Html::a('Reply', '#reply', [
                    'class' => 'btn btn-sm btn-link pull-right',
                    'data' => [
                        'key' => $comment['id']
                    ]
                ]);

            } else {
                $item .= '&nbsp;';
            }
            $item .= '</div>'; // .media-footer


            if (isset($comment['items'])) {
                if (is_array($comment['items'])) {
                    $item .= $this->buildCommentsTree($comment['items']);
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

        if ($isRootLevel)
            return Html::tag($listRootTag, $output, $listRootOptions);
        else
            return $output;
    }

}