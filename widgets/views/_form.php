<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var $this \yii\web\View
 * @var $id string, as `formId` property from CommentsWidget::widget()
 * @var $action string, as `formAction` property from CommentsWidget::widget()
 * @var $options string, as `formOptions` property from CommentsWidget::widget()
 * @var $model object, instance of \wdmg\comments\models\Comments
 */

$form = ActiveForm::begin([
    'id' => $id, // as `formId` property from CommentsWidget::widget()
    'action' => $action, // as `formAction` property from CommentsWidget::widget()
    'options' => $options // as `formOptions` property from CommentsWidget::widget()
]);

?>
<h4 class="page-header"><?= Yii::t('app/modules/comments','Leave a comment'); ?></h4>
<?php
    if ($user = Yii::$app->getUser()->isGuest) :
?>
<div class="row">
    <?php

        echo $form->field($model, 'name', [
            'labelOptions' => [
                'class' => 'control-label col-xs-12 col-xs-6 col-md-3'
            ],
            'template' => '{label}<div class="col-xs-12 col-sm-6 col-md-9">{input}</div><div class="col-xs-12 col-sm-6 col-sm-offset-6 col-md-9 col-md-offset-3">{error}</div>',
        ])->textInput();

        echo $form->field($model, 'email', [
            'labelOptions' => [
                'class' => 'control-label col-xs-12 col-xs-6 col-md-3'
            ],
            'template' => '{label}<div class="col-xs-12 col-sm-6 col-md-9">{input}</div><div class="col-xs-12 col-sm-6 col-sm-offset-6 col-md-9 col-md-offset-3">{error}</div>',
        ])->textInput();

    ?>
</div>
<?php
    endif;
?>

<?= $form->field($model, 'comment')->textarea(); ?>
<div class="form-group">
    <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'parent_id')->hiddenInput()->label(false); ?>
    <?= Html::submitButton(Yii::t('app/modules/comments','Submit'), ['class' => 'btn btn-primary']); ?>
</div>
<?php ActiveForm::end(); ?>
