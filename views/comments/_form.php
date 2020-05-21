<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wdmg\widgets\LangSwitcher;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $model wdmg\comments\models\Comments */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comments-form">
    <?php
        $form = ActiveForm::begin([
            'id' => "editComment",
            'enableAjaxValidation' => true,
            'options' => [
                'enctype' => 'multipart/form-data'
            ]
        ]);
    ?>

    <?= $form->field($model, 'comment')->textarea() ?>
    <?= $form->field($model, 'status')->widget(SelectInput::class, [
        'items' => $model->getStatusesList(false),
        'options' => [
            'class' => 'form-control'
        ]
    ]) ?>

    <div class="modal-footer">
        <?= Html::a(Yii::t('app/modules/comments', 'Close'), "#", [
            'class' => 'btn btn-default pull-left',
            'data-dismiss' => 'modal'
        ]); ?>
        <?= Html::submitButton(Yii::t('app/modules/comments', 'Save'), ['class' => 'btn btn-save btn-success pull-right']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>