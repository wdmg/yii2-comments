<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var $this \yii\web\View
 * @var $id string, as `formId` property from CommentsWidget::widget()
 * @var $action string, as `formAction` property from CommentsWidget::widget()
 * @var $options array, as `formOptions` property from CommentsWidget::widget()
 * @var $template array, as `formTemplete` property from CommentsWidget::widget()
 * @var $model object, instance of \wdmg\comments\models\Comments
 * @var $bundle object, instance of \wdmg\comments\CommentsAsset()
 */

$form = ActiveForm::begin([
    'id' => $id,
    'action' => $action,
    'options' => (isset($options['formOptions'])) ? $options['formOptions'] : []
]);


// Build form header
$header = Html::tag(
    (isset($options['headerTag'])) ? $options['headerTag'] : 'h4',
    (isset($options['headerLabel'])) ? $options['headerLabel'] : Yii::t('app/modules/comments','Leave a comment'),
    (isset($options['headerOptions'])) ? $options['headerOptions'] : []
);


// Build name input
$name = $form->field($model, 'name', (isset($options['nameOptions'])) ? $options['nameOptions'] : [])->textInput();

if (isset($options['nameLabel']))
    $name->label($options['nameLabel'], (isset($options['nameOptions']['labelOptions'])) ? $options['nameOptions']['labelOptions'] : []);


// Build email input
$email = $form->field($model, 'email', (isset($options['emailOptions'])) ? $options['emailOptions'] : [])->textInput();

if (isset($options['emailLabel']))
    $email->label($options['emailLabel'], (isset($options['emailOptions']['labelOptions'])) ? $options['emailOptions']['labelOptions'] : []);


// Build comment textarea
$comment = $form->field($model, 'comment', (isset($options['commentOptions'])) ? $options['commentOptions'] : [])->textarea();

if (isset($options['commentLabel']))
    $comment->label($options['commentLabel'], (isset($options['commentOptions']['labelOptions'])) ? $options['commentOptions']['labelOptions'] : []);


// Build submit button
$submit  = $form->field($model, 'id')->hiddenInput()->label(false);
$submit .= $form->field($model, 'parent_id')->hiddenInput()->label(false);
$submit .= Html::submitButton(
    (isset($options['submitLabel'])) ? $options['submitLabel'] : Yii::t('app/modules/comments','Submit'),
    (isset($options['submitOptions'])) ? $options['submitOptions'] : []
);

// Echo (return) the form
echo str_replace([
    '{header}',
    '{name}',
    '{email}',
    '{comment}',
    '{submit}'
], [
    $header,
    $name,
    $email,
    $comment,
    $submit
], $template);

ActiveForm::end();

?>