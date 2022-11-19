<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TahunBulan $model */
/** @var yii\widgets\ActiveForm $form */

$list_bulan = [
    'Januari' => 'Januari',
    'Februari' => 'Februari',
    'Maret' => 'Maret',
    'April' => 'April',
    'Mei' => 'Mei',
    'Juni' => 'Juni',
    'Juli' => 'Juli',
    'Agustus' => 'Agustus',
    'September' => 'September',
    'Oktober' => 'Oktober',
    'November' => 'November',
    'Desember' => 'Desember',
]
?>

<div class="tahun-bulan-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'bulan')->dropDownList($list_bulan, ['prompt' => 'Pilih bulan...']) ?>
    
    <?= $form->field($model, 'tahun')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>