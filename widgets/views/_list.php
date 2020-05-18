<?php

use wdmg\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<h3 class="page-header"><?= Yii::t('app/modules/comments','Comments ({0})', $count); ?></h3>

<?php

echo buildCommentsTree($comments, $id, $form_id, $options, $bundle);

function buildCommentsTree($comments = [], $listId = null, $formId = null, $options = null, $bundle = null) {

    $output = '';
    $userId = Yii::$app->getUser()->getId();

    if (isset($options['listRootTag']))
        $listRootTag = $options['listRootTag'];
    else
        $listRootTag = 'ul';

    if (isset($options['listRootOptions']))
        $listRootOptions = $options['listRootOptions'];
    else
        $listRootOptions = [];


    if (isset($options['itemRootTag']))
        $itemRootTag = $options['itemRootTag'];
    else
        $itemRootTag = 'li';

    if (isset($options['itemRootOptions']))
        $itemRootOptions = $options['itemRootOptions'];
    else
        $itemRootOptions = [];


    if (isset($options['itemReplyTag']))
        $itemReplyTag = $options['itemReplyTag'];
    else
        $itemReplyTag = 'div';

    if (isset($options['itemReplyOptions']))
        $itemReplyOptions = $options['itemReplyOptions'];
    else
        $itemReplyOptions = [];


    if (isset($options['userPhotoSize']))
        $userPhotoSize = $options['userPhotoSize'];

    if (isset($options['userPhotoAlign']))
        $userPhotoAlign = $options['userPhotoAlign'];
    else
        $userPhotoAlign = 'left';

    if (isset($options['userPhotoOptions']))
        $userPhotoOptions = $options['userPhotoOptions'];
    else
        $userPhotoOptions = [];

    if (isset($options['userLinkOptions']))
        $userLinkOptions = $options['userLinkOptions'];
    else
        $userLinkOptions = [];

    if (isset($options['editTimeout']))
        $editTimeout = $options['editTimeout'];
    else
        $editTimeout = false;

    foreach($comments as $comment) {

        $item = '';
        $isRootLevel = false;

        if (isset($comment['_level'])) {

            if (intval($comment['_level']) == 1)
                $isRootLevel = true;

        }

        if ($options['userPhotoSize'] == 32) {
            $userDefaultPhoto = $bundle->baseUrl . "/images/user-default-32.png";
            $userPhotoSizeClass = ["size-32x32"];
        } else if ($options['userPhotoSize'] == 96) {
            $userDefaultPhoto = $bundle->baseUrl . "/images/user-default-96.png";
            $userPhotoSizeClass = ["size-96x96"];
        } else if ($options['userPhotoSize'] == 128) {
            $userDefaultPhoto = $bundle->baseUrl . "/images/user-default-128.png";
            $userPhotoSizeClass = ["size-128x128"];
        } else {
            $userDefaultPhoto = $bundle->baseUrl . "/images/user-default-64.png";
            $userPhotoSizeClass = ["size-64x64"];
        }

        if ($comment['name'] == 'Bob')
            $comment['photo'] = 'https://bootdey.com/img/Content/avatar/avatar6.png';

        if ($comment['name'] == 'Alice')
            $comment['photo'] = 'https://bootdey.com/img/Content/avatar/avatar5.png';

        if ($options['useGravatar'] && isset($comment['email']) && !isset($comment['photo'])) {
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

        $item .= Html::tag('div', $comment['comment'], [
            'class' => "comment-text"
        ]);

        $item .= '</div>'; // .media-content

        $item .= '<div class="media-footer">';
        if ($editTimeout && !is_null($userId)) {
            if (
                ((intval($comment['user_id']) == $userId) && !is_null($userId)) ||
                ($comment['user_id'] == \Yii::$app->session->getId())
            ) {
                $item .= Html::a('Delete', '#delete', [
                    'class' => 'btn btn-sm btn-link pull-right',
                    'data' => [
                        'toggle' => 'comments-delete',
                        'key' => $comment['id']
                    ]
                ]);

                if (intval($editTimeout) >= (time() - strtotime($comment['updated_at']))) {
                    $item .= Html::a('Edit', '#edit', [
                        'class' => 'btn btn-sm btn-link pull-right',
                        'data' => [
                            'toggle' => 'comments-edit',
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
                    'toggle' => 'comments-abuse',
                    'key' => $comment['id']
                ]
            ]);

            $item .= Html::a('Reply', '#reply', [
                'class' => 'btn btn-sm btn-link pull-right',
                'data' => [
                    'toggle' => 'comments-reply',
                    'key' => $comment['id']
                ]
            ]);

        } else {
            $item .= '&nbsp;';
        }
        $item .= '</div>'; // .media-footer


        if (isset($comment['items'])) {
            if (is_array($comment['items'])) {
                $item .= buildCommentsTree($comment['items'], $listId, $formId, $options, $bundle);
            }
        }

        $item .= '</div>'; // .media-body

        if ($userPhotoAlign == 'right') {
            $item .= Html::tag('div', $link, ['class' => 'media-right']);
        }

        if ($isRootLevel) {
            $output .= Html::tag($itemRootTag, $item, ArrayHelper::merge($itemRootOptions, [
                'data' => [
                    'comment-id' => $comment['id']
                ],
            ]));
        } else {
            $output .= Html::tag($itemReplyTag, $item, ArrayHelper::merge($itemReplyOptions, [
                'data' => [
                    'comment-id' => $comment['id']
                ],
            ]));
        }
    }

    if ($isRootLevel)
        return Html::tag($listRootTag, $output, ArrayHelper::merge($listRootOptions, [
            'id' => $listId,
            'data' => [
                'target' => '#' . $formId
            ]
        ]));
    else
        return $output;
}