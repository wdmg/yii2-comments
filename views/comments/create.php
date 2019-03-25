<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model wdmg\comments\models\Comments */

$this->title = Yii::t('app/modules/comments', 'Create Comments');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/comments', 'Comments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comments-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
