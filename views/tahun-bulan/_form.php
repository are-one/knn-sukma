<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

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
];
?>

<div class="bs-example1" data-example-id="contextual-table">

    <div class="tahun-bulan-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'bulan')->widget(Select2::class,[
            'data' => $list_bulan,
            'options' => ['placeholder' => 'Pilih bulan ...', 'class' => 'form-control1'],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ])->label('Bulan') ?>
        
        <?= $form->field($model, 'tahun')->textInput(['class' => 'form-control1']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
