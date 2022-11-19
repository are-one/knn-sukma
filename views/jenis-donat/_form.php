<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\JenisDonat $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="jenis-donat-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'jenis_donat')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
