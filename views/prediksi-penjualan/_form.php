<?php

use app\models\JenisBarang;
use app\models\TahunBulan;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\PrediksiPenjualan $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="prediksi-penjualan-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-group">
        <?= $form->field($modelDataPrediksi, 'jenis_barang_id')->widget(Select2::class,[
            'data' => ArrayHelper::map(JenisBarang::find()->all(), 'id',function($model)
            {
                return $model->jenis_barang;
            }),
            'options' => ['placeholder' => 'Pilih jenis barang ...'],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ])->label('Jenis Barang') ?>
    </div>

    <div class="form-group">
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
    </div>

    <div class="row">
        <?php for($i = 0; $i < 10; $i++){ ?>
            <div class="form-group col-sm-4">
                <?= $form->field($modelDataPrediksi, "jumlah_penjualan[{$i}]", [
                    'options' => ['class' => 'col-3 mb-3']
                ])->textInput(['value' => 0,'type' => 'number', 'min' => 0]) ?>
            </div>
        <?php } ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
