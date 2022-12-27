<?php

use app\models\JenisBarangHasPenjualan;
use app\models\JenisBarangHasPrediksiPenjualan;
use app\models\Penjualan;
use app\models\PrediksiPenjualan;
use yii\bootstrap\Modal;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\PrediksiPenjualan $model */

$this->title = 'Detail Hasil';
$this->params['breadcrumbs'][] = ['label' => 'Prediksi Penjualan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$icons = (new ActionColumn())->icons;

?>
<div class="bs-example1" data-example-id="contextual-table"> 

    <h1><?= Html::encode($this->title) ?></h1>

    <hr>
    <p style="margin-bottom: 10px;">
        <?php // Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
            ]) ?>
        <?= Html::a('Refresh', ['refresh', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
            [
                'attribute' => 'hasil_prediksi',
                'captionOptions' => ['width' => '20%']
            ],
            'tahunBulan.bulan',
        ],
    ]) ?>

    <div class="table-responsive">
        <table class="table table-bordered">
                <thead>
                    <tr>
                        <th colspan="8" class="text-center">Jumlah Penjualan</th>
                        <th class="text-center" width="10%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 

                    if(!empty($model->jenisBarangHasPrediksiPenjualans)){
                       
                            $jumlahPenjualan = $model->jenisBarangHasPrediksiPenjualans;
                            $jenis_barang_id = null;
                            $data_prediksi = [];
                        ?>
                        <tr>
                            <?php
                            $total = 0;
                            foreach ($jumlahPenjualan as $_ => $jp) {    
                                $data_prediksi[] = $jp->jumlah_penjualan;
                                $jenis_barang_id = $jp->jenis_barang_id;
                            ?>
                                    <td class="text-center"><?= $jp->jumlah_penjualan ?></td>
                            <?php 
                            $total += $jp->jumlah_penjualan;
                                } 
                            ?>
                            <td class="text-center"><?= $total ?></td>
                        </tr>
                                
                        <?php 
                    }else{
                        ?>
                                <tr>
                                    <td class="text-center" colspan="12">
                                        <i class="text-muted">Data tidak ditemukan</i>
                                    </td>
                                </tr>
                        <?php
                            }
                    ?>
    
                </tbody>
        </table>
    </div>

    <!-- ==================================================================================================================== -->
    <?php
        $data = PrediksiPenjualan::getSampelLabel($jenis_barang_id, 8);
        $samplesTraining = $data['sample'];
        $label = $data['label'];
        $id_sample = $data['id_sample'];
        
        // Normalisasi
        $summaryMinMax = PrediksiPenjualan::getMinMaxData($samplesTraining);
        $dataTrainingNormalisasi = PrediksiPenjualan::normalisasiData($samplesTraining, $summaryMinMax);
        
        // Hitung distance sebelum tentukan nilai k
        $distances_tanpa_k = PrediksiPenjualan::hitungDistanceTraining($jenis_barang_id, $dataTrainingNormalisasi, $data_prediksi);
        
        // Hitung distance dengan K = 3
        $distances = PrediksiPenjualan::hitungDistanceTraining($jenis_barang_id, $dataTrainingNormalisasi, $data_prediksi, 3);
        ?>
    <!-- ==================================================================================================================== -->



    <p>Data Training</p>
    <div class="table-responsive">
        <table class="table table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
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

                    foreach ($samplesTraining as $index => $data) {
                        $id_penjualan = $id_sample[$index];
                        $penjualan = Penjualan::findOne(['id' => $id_penjualan]);
                        $total = null;
                        if($penjualan != null){
                    ?>

                        <tr>
                            <td><?= $i ?></td>                
                            <td>
                                <?= $penjualan->tahunBulan->bulan . ' - '. $penjualan->tahunBulan->tahun ?>
                            </td>
                    
                            <?php
                            foreach ($data as $col => $nilai) {
                                $total += $nilai;   
                            ?>
                                <td class="p-0">
                                    <?= $nilai ?>
                                </td>
                            <?php } ?>

                            <td class="text-center"><?= $total ?></td>
                            <td class="text-center">
                                        <?= $penjualan->label ?>
                            </td>
                        </tr>
                                
                        <?php 
                        } 

                        $i++;
                       
                    }

                    if($samplesTraining == null){

                        ?>
    
                            <tr>
                                <td class="text-center" colspan="13">
                                    <i class="text-muted">Data tidak ditemukan</i>
                                </td>
                            </tr>
                    <?php } ?>
                </tbody>
        </table>
    </div>
        

    <p>Proses KNN</p>
    <div class="table-responsive">
        <table class="table table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2" width="25%">Tahun - Bulan</th>
                        <th colspan="8" width="17%" class="text-center">Jumlah Penjualan</th>
                        <th rowspan="2" class="text-center">Jarak</th>
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
                    $kesimpulan = [];
                    foreach ($distances_tanpa_k as $index => $nilai_distance) {
                        $id_penjualan = $id_sample[$index];
                        $penjualan = Penjualan::findOne(['id' => $id_penjualan]);
                        $total = null;
                        if($penjualan != null){
                            $kesimpulan[$penjualan->label] = 0;
                    ?>

                        <tr>
                            <td><?= $i ?></td>                
                            <td>
                                <?= $penjualan->tahunBulan->bulan . ' - '. $penjualan->tahunBulan->tahun ?>
                            </td>
                    
                            <?php
                            foreach ($dataTrainingNormalisasi[$index] as $col => $nilai) {
                                $total += $nilai;   
                            ?>
                                <td class="p-0">
                                    <?= $nilai ?>
                                </td>
                            <?php } ?>

                            <td class="text-center"><?= $nilai_distance ?></td>
                            <td class="text-center">
                                        <?= $penjualan->label ?>
                            </td>
                        </tr>
                                
                        <?php 
                        } 

                        $i++;
                       
                    }

                    if($dataTrainingNormalisasi == null){

                        ?>
    
                            <tr>
                                <td class="text-center" colspan="13">
                                    <i class="text-muted">Data tidak ditemukan</i>
                                </td>
                            </tr>
                    <?php } ?>
                </tbody>
        </table>
    </div>

    <p>K = 3</p>
    <div class="table-responsive">
        <table class="table table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2" width="25%">Tahun - Bulan</th>
                        <th colspan="8" width="17%" class="text-center">Jumlah Penjualan</th>
                        <th rowspan="2" class="text-center">Jarak</th>
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
                    foreach ($distances as $index => $nilai_distance) {
                        $id_penjualan = $id_sample[$index];
                        $penjualan = Penjualan::findOne(['id' => $id_penjualan]);
                        $total = null;
                        if($penjualan != null){
                            ++$kesimpulan[$penjualan->label];
                    ?>

                        <tr>
                            <td><?= $i ?></td>                
                            <td>
                                <?= $penjualan->tahunBulan->bulan . ' - '. $penjualan->tahunBulan->tahun ?>
                            </td>
                    
                            <?php
                            foreach ($dataTrainingNormalisasi[$index] as $col => $nilai) {
                                $total += $nilai;   
                            ?>
                                <td class="p-0">
                                    <?= $nilai ?>
                                </td>
                            <?php } ?>

                            <td class="text-center"><?= $nilai_distance ?></td>
                            <td class="text-center">
                                        <?= $penjualan->label ?>
                            </td>
                        </tr>
                                
                        <?php 
                        } 

                        $i++;
                       
                    }

                    if($dataTrainingNormalisasi == null){

                        ?>
    
                            <tr>
                                <td class="text-center" colspan="13">
                                    <i class="text-muted">Data tidak ditemukan</i>
                                </td>
                            </tr>
                    <?php } ?>
                </tbody>
        </table>
    </div>

    <p>Kesimpulan</p>
   <div class="row">
    <div class="col-md-6">
    <div class="table-responsive">
        <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Hasil</th>
                        <th width="50%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    arsort($kesimpulan);
                    foreach ($kesimpulan as $label => $total) {
                    ?>

                        <tr>
                            <td><?= $label ?></td>                
                            <td>
                                <?= $total ?>
                            </td>
                        </tr>
                                
                        <?php 
                       
                    }

                    if($kesimpulan == null){

                        ?>
    
                            <tr>
                                <td class="text-center" colspan="2">
                                    <i class="text-muted">Data tidak ditemukan</i>
                                </td>
                            </tr>
                    <?php } ?>
                </tbody>
        </table>
    </div>
    </div>
   </div>



</div>
