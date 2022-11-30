<?php

use app\models\JenisBarang;
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
    <div class="alert alert-info">
        <span>Jika jenis barang pada bulan yang dipilih sudah pernah diinputkan sebelummnya, maka label yang lama akan diperbaharui sesuai label yang dipilih saat ini</span>
    </div>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($modelJenisBarangHasPenjualan, 'jenis_barang_id')->widget(Select2::class,[
        'data' => ArrayHelper::map(JenisBarang::find()->all(), 'id',function($model)
        {
            return $model->jenis_barang;
        }),
        'options' => ['placeholder' => 'Pilih jenis barang ...'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])->label('Jenis Barang') ?>


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

<?= $form->field($model, 'label')->widget(Select2::class,[
    'data' => ['Sedikit' => 'Sedikit', 'Terbanyak' => 'Terbanyak'],
    'options' => ['placeholder' => 'Pilih label ...'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
    ])->label('Label') ?>

    <div class="row">
        <?php for($i = 0; $i < 10; $i++){ ?>
            <?= $form->field($modelJenisBarangHasPenjualan, "jumlah_penjualan[{$i}]", [
                'options' => ['class' => 'col-3 mb-3']
            ])->textInput(['value' => 0, 'type' => 'number', 'min' => 0]) ?>
        <?php } ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
