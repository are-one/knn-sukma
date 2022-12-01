<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\JenisDonat $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="bs-example1" data-example-id="contextual-table">

    <div class="jenis-barang-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'jenis_barang')->textInput(['maxlength' => true, 'class' => 'form-control1']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>