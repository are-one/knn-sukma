<?php

use app\models\JenisDonat;
use app\models\TahunBulan;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\Penjualan $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="penjualan-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tahun_bulan_id')->widget(Select2::class,[
        'data' => ArrayHelper::map(TahunBulan::find()->all(), 'id',function($model)
        {
            return $model->bulan . ' - ' . $model->tahun;
        }),
        'options' => ['placeholder' => 'Pilih bulan-tahun ...'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])->label('Bulan - Tahun') ?>

    <?= $form->field($model, 'jenis_donat_id')->widget(Select2::class,[
        'data' => ArrayHelper::map(JenisDonat::find()->all(), 'id',function($model)
        {
            return $model->jenis_donat;
        }),
        'options' => ['placeholder' => 'Pilih jenis donat ...'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])->label('Jenis Donat') ?>

    <?= $form->field($model, 'jumlah_penjualan')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
