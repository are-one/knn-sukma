<?php

use app\models\JenisDonatHasPrediksiPenjualan;
use app\models\PrediksiPenjualan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\search\PrediksiPenjualanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Hasil Prediksi Penjualan';
$this->params['breadcrumbs'][] = $this->title;

$icons = (new ActionColumn())->icons;

?>
<div class="prediksi-penjualan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Prediksi Data Penjualan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="table-responsive">
        <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis Donat</th>
                        <th width="30%">Tahun - Bulan</th>
                        <th width="36%">Jumlah Penjualan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;

                    foreach ($jenisDonat as $_ => $donat) {
                        $penjualan = PrediksiPenjualan::find()->joinWith(['jenisDonatHasPrediksiPenjualans'])->select('tahun_bulan_id, prediksi_penjualan.id, prediksi_penjualan.hasil_prediksi')->distinct()->where(['jenis_donat_has_prediksi_penjualan.jenis_donat_id' => $donat->id])->orderBy(['tahun_bulan_id' => SORT_ASC])->all();
                        $jumlahPenjualan = null;
                        if($penjualan != null){
                            
                    ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= $donat->jenis_donat ?></td>
                        <td colspan="2" class="p-0">
                            <table class="table table-bordered mb-0" width="100%">
                                <?php
                                foreach ($penjualan as $_ => $p) {
                                    $jumlahPenjualan = JenisDonatHasPrediksiPenjualan::find()->joinWith(['prediksiPenjualan'])->where(['jenis_donat_id' => $donat->id, 'prediksi_penjualan_id' => $p->id])->orderBy(['prediksi_penjualan.tahun_bulan_id' => SORT_ASC])->all();
                                ?>
                                    <tr>
                                        <td width="45%">
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
                                            <?= $p->hasil_prediksi ?>
                                        </td>
                                    </tr>
                                    <?php 
                                    } 
                                    ?>
                            </table>
                        </td>
                    </tr>
                    <?php 
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
                    ?>
                </tbody>
            </table>
   </div>



</div>
