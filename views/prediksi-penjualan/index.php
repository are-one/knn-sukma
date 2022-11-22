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
                            $penjualan = PrediksiPenjualan::find()->select('tahun_bulan_id')->distinct()->orderBy(['tahun_bulan_id' => SORT_ASC])->all();
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
                                    $jumlahPenjualan = JenisDonatHasPrediksiPenjualan::find()->where(['jenis_donat_id' => $donat->id, 'prediksi_penjualan_id' => $p->id])->orderBy(['tahun_bulan_id' => SORT_ASC])->all();
                                    
                                ?>
                                    <tr>
                                        <td width="45%"><?= $p->tahunBulan->bulan . ' - '. $p->tahunBulan->tahun ?></td>
                                        <td class="p-0">
                                        <table class="table table-bordered mb-0">
                                            <?php
                                            foreach ($jumlahPenjualan as $_ => $jp) {    
                                            ?>
                                                <tr>
                                                    <td><?= $jp->jumlah_penjualan ?></td>
                                                </tr>
                                            <?php 
                                                } 
                                            ?>
                                        </table>
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
