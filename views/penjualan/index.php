<?php

use app\models\JenisBarangHasPenjualan;
use app\models\Penjualan;
use yii\grid\ActionColumn;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\search\PenjualanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Data Training';
$this->params['breadcrumbs'][] = $this->title;

$icons = (new ActionColumn())->icons;
?>
<div class="penjualan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <hr>
    <p>
        <?= Html::a('Tambah Data Penjualan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="table-responsive">
        <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th width="25%">Jenis Barang</th>
                        <th width="25%">Tahun - Bulan</th>
                        <th width="17%">Jumlah Penjualan</th>
                        <th class="text-center">Label</th>
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
                            $jumlahPenjualan = JenisBarangHasPenjualan::find()->joinWith(['penjualan'])->where(['jenis_barang_id' => $barang->id, 'penjualan_id' => $p->id])->orderBy(['penjualan.tahun_bulan_id' => SORT_ASC])->all();
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

                            <td class="p-0">
                                <table class="table table-bordered mb-0">
                                    <?php
                                    foreach ($jumlahPenjualan as $_ => $jp) {    
                                    ?>
                                        <tr>
                                            <td><?= $jp->jumlah_penjualan ?></td>
                                            <td width="4%">
                                                <?= Html::a($icons['trash'], ['prediksi-penjualan/delete-jumlah-penjualan','id' => $jp->id], ['data' => [
                                                                        'method' => 'post',
                                                                        'confirm' => 'Are you sure you want to delete this item?',
                                                                    ]
                                                                ]) ?>
                                            </td>
                                        </tr>
                                    <?php 
                                        } 
                                    ?>
                                </table>
                            </td>
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
                                        <td class="text-center" colspan="4">
                                            <i class="text-muted">Data tidak ditemukan</i>
                                        </td>
                                    </tr>
                            <?php
                                }
                    }

                    if($jenisBarang == null){

                        ?>
    
                            <tr>
                                <td class="text-center" colspan="5">
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
