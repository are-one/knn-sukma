<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\PrediksiPenjualan $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="prediksi-penjualan-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'hasil_prediksi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tahun_bulan_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
