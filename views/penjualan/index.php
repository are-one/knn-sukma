<?php

use app\models\JenisBarang;
use app\models\JenisBarangHasPenjualan;
use app\models\Penjualan;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/** @var yii\web\View $this */
/** @var app\models\search\PenjualanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Data Training';
$this->params['breadcrumbs'][] = $this->title;

// $this->registerJsFile('@web/template/js/jquery.min.js', ['position' => View::POS_END]);

$icons = (new ActionColumn())->icons;
?>
<div class="penjualan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="bs-example1" data-example-id="contextual-table"> 
        <p>
            <?= Html::a('Download Template Input Excel', ['template'], ['class' => 'btn btn-info']); ?>

            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#upload">Upload Data</button>
            <?= Html::a('Tambah Data', ['create'], ['class' => 'btn btn-info']) ?>
            <?= Html::a('Hapus Semua', ['penjualan/delete-all'], ['class' => 'btn btn-danger']) ?>
        </p>
        
        <hr>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <div class="table-responsive">
            <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2" width="25%">Jenis Barang</th>
                            <th rowspan="2" width="25%">Tahun - Bulan</th>
                            <th colspan="8" width="17%" class="text-center">Jumlah Penjualan</th>
                            <th rowspan="2" class="text-center">Total</th>
                            <th rowspan="2" class="text-center">Hasil</th>
                        </tr>
                        <tr>
                            <?php 
                            $jumlah_data = 8;
                            for($i=1; $i<= $jumlah_data; $i++): ?>
                                <th><?= $i ?></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;

                        foreach ($jenisBarang as $_ => $barang) {
                            $penjualan = Penjualan::find()->joinWith(['jenisBarangHasPenjualans'])->select('tahun_bulan_id, penjualan.id, penjualan.label')->distinct()->where(['jenis_barang_has_penjualan.jenis_barang_id' => $barang->id])->orderBy(['tahun_bulan_id' => SORT_ASC])->all();
                            $jumlahPenjualan = null;
                            if($penjualan != null){
                        ?>

                        <tr>
                            <td rowspan="<?= count($penjualan) + 1 ?>"><?= $i ?></td>
                            <td rowspan="<?= count($penjualan) + 1 ?>"><?= $barang->jenis_barang ?></td>
                        
                        </tr>

                        <?php
                            foreach ($penjualan as $_ => $p) {
                                $jumlahPenjualan = JenisBarangHasPenjualan::find()->joinWith(['penjualan'])->where(['jenis_barang_id' => $barang->id, 'penjualan_id' => $p->id])->orderBy(['id' => SORT_ASC])->all();
                            ?>
                            <tr>
                                <td>
                                    <?= $p->tahunBulan->bulan . ' - '. $p->tahunBulan->tahun ?>
                                    <?= Html::a($icons['trash'], ['penjualan/delete','id' => $p->id], [
                                                                'class' => 'float-end',
                                                                'data' => [
                                                                            'method' => 'post',
                                                                            'confirm' => 'Menghapus data Tahun-Bulan akan menghapus seluruh data jumlah penjualan yang berkaitan data ini. Apakah anda yakin ingin?',
                                                                        ]
                                                                    ]) ?>
                                </td>

                                <?php
                                $total = 0;
                                foreach ($jumlahPenjualan as $_ => $jp) { 
                                    $total += $jp->jumlah_penjualan;   
                                ?>
                                    <td class="p-0">
                                        <?= $jp->jumlah_penjualan ?>
                                    </td>
                                <?php 
                                    } 
                                ?>
                                <td class="text-center"><?= $total ?></td>
                                <td class="text-center">
                                            <?= $p->label ?>
                                </td>
                            </tr>
                                    
                            <?php 
                                } 
                            
                                $i++;
                            }else{
                                ?>
                                        <tr>
                                            <td class="text-center" colspan="12">
                                                <i class="text-muted">Data tidak ditemukan</i>
                                            </td>
                                        </tr>
                                <?php
                                    }
                        }

                        if($jenisBarang == null){

                            ?>
        
                                <tr>
                                    <td class="text-center" colspan="13">
                                        <i class="text-muted">Data tidak ditemukan</i>
                                    </td>
                                </tr>
                        <?php
                                    
                        }
                        ?>
                    </tbody>
            </table>
        </div>

</div>

<?php 
Modal::begin([
    'options' => ['id' => 'upload'],
    'header' => '<h4>Upload Data</h4>',
]);
    $form = ActiveForm::begin(['action' => ['upload'], 'options' => [ 'enctype' => 'multipart/form-data']]);
?>
    <div class="form-group">
        <?= $form->field($modelJenisBarangHasPenjualan, 'jenis_barang_id')->widget(Select2::class,[
            'data' => ArrayHelper::map(JenisBarang::find()->all(), 'id', 'jenis_barang'),
            'options' => [
                'name' => 'jenis_barang_id',
                'placeholder' => 'Pilih jenis barang ...',
            ],
        ]); ?>
    </div>

    <div class="form-group">
        <?= Html::fileInput('file_data', null, ['class' => 'form-control', 'accept' => '.xlsx, .xls']); ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Simpan',['class' => 'btn btn-primary']) ?>
    </div>

<?php
    ActiveForm::end();
Modal::end();
