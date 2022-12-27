<?php

use app\models\JenisBarangHasPenjualan;
use app\models\JenisBarangHasPrediksiPenjualan;
use app\models\Penjualan;
use app\models\PrediksiPenjualan;
use Phpml\Math\Distance\Euclidean;
use yii\helpers\Html;


$dataTraining = PrediksiPenjualan::getSampelLabel(1, 8);
$samples = $dataTraining['sample'];
// print_r($samples);die;
$label = $dataTraining['label'];
$test = [1000,2908,4550,1278,100,50,124,50];
$k = 3;

$distances = [];
$distaceMatrict = new Euclidean();

foreach ($samples as $index => $neighbor) {
    $distances[$index] = $distaceMatrict->distance($test, $neighbor);
}

asort($distances);

$hasil_distance = array_slice($distances, 0, $k, true);


$predictions = (array) array_combine(array_values($label), array_fill(0, count($label), 0));

foreach (array_keys($hasil_distance) as $index) {
    ++$predictions[$label[$index]];
}

arsort($predictions);
reset($predictions);

$hasil_akhir = key($predictions);

?>
<div class="prediksi-penjualan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="bs-example1" data-example-id="contextual-table"> 

        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        <p>Data Training</p>
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
        
        <p>Data Testing</p>
        <div class="table-responsive">
            <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2" width="25%">Jenis Barang</th>
                            <th rowspan="2" width="25%">Tahun - Bulan</th>
                            <th colspan="8" width="17%" class="text-center">Jumlah Penjualan</th>
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
                            $penjualan = PrediksiPenjualan::find()->joinWith(['jenisBarangHasPrediksiPenjualans'])->select('tahun_bulan_id, prediksi_penjualan.id, prediksi_penjualan.hasil_prediksi')->distinct()->where(['jenis_barang_has_prediksi_penjualan.jenis_barang_id' => $barang->id])->orderBy(['tahun_bulan_id' => SORT_ASC])->all();
                            $jumlahPenjualan = null;
                            
                        if($penjualan != null){
                        ?>

                        <tr>
                            <td rowspan="<?= count($penjualan) + 1 ?>"><?= $i ?></td>
                            <td rowspan="<?= count($penjualan) + 1 ?>"><?= $barang->jenis_barang ?></td>
                        
                        </tr>

                        <?php
                            foreach ($penjualan as $_ => $p) {
                                $jumlahPenjualan = JenisBarangHasPrediksiPenjualan::find()->joinWith(['prediksiPenjualan'])->where(['jenis_barang_id' => $barang->id, 'prediksi_penjualan_id' => $p->id])->orderBy(['id' => SORT_ASC])->all();
                            ?>
                            <tr>
                                <td>
                                    <?= $p->tahunBulan->bulan . ' - '. $p->tahunBulan->tahun ?>
                                    <?= Html::a($icons['trash'], ['prediksi-penjualan/delete','id' => $p->id], [
                                                                'class' => 'float-end',
                                                                'data' => [
                                                                            'method' => 'post',
                                                                            'confirm' => 'Menghapus data Tahun-Bulan akan menghapus seluruh data jumlah penjualan yang berkaitan data ini. Apakah anda yakin ingin?',
                                                                        ]
                                                                    ]) ?>
                                </td>

                                <?php
                                foreach ($jumlahPenjualan as $_ => $jp) {    
                                ?>
                                        <td><?= $jp->jumlah_penjualan ?></td>
                                <?php 
                                    } 
                                ?>
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

        <p>Proses KNN</p>
        <div class="table-responsive">
            <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2" width="25%">Jenis Barang</th>
                            <th rowspan="2" width="25%">Tahun - Bulan</th>
                            <th colspan="8" width="17%" class="text-center">Jumlah Penjualan</th>
                            <th rowspan="2" class="text-center">Total</th>
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
                            $penjualan = PrediksiPenjualan::find()->joinWith(['jenisBarangHasPrediksiPenjualans'])->select('tahun_bulan_id, prediksi_penjualan.id, prediksi_penjualan.hasil_prediksi')->distinct()->where(['jenis_barang_has_prediksi_penjualan.jenis_barang_id' => $barang->id])->orderBy(['tahun_bulan_id' => SORT_ASC])->all();
                            $jumlahPenjualan = null;
                            
                        if($penjualan != null){
                        ?>

                        <tr>
                            <td rowspan="<?= count($penjualan) + 1 ?>"><?= $i ?></td>
                            <td rowspan="<?= count($penjualan) + 1 ?>"><?= $barang->jenis_barang ?></td>
                        
                        </tr>

                        <?php
                            foreach ($penjualan as $_ => $p) {
                                $jumlahPenjualan = JenisBarangHasPrediksiPenjualan::find()->joinWith(['prediksiPenjualan'])->where(['jenis_barang_id' => $barang->id, 'prediksi_penjualan_id' => $p->id])->orderBy(['id' => SORT_ASC])->all();
                            ?>
                            <tr>
                                <td>
                                    <?= $p->tahunBulan->bulan . ' - '. $p->tahunBulan->tahun ?>
                                    <?= Html::a($icons['trash'], ['prediksi-penjualan/delete','id' => $p->id], [
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
                                        <td><?= $jp->jumlah_penjualan ?></td>
                                <?php 
                                    } 
                                ?>
                                <td class="text-center"><?= $total ?></td>
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

    </div>

</div>
