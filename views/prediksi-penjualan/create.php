<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\PrediksiPenjualan $model */

$this->title = 'Masukkan Data Penjualan';
$this->params['breadcrumbs'][] = ['label' => 'Prediksi Penjualan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prediksi-penjualan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelDataPrediksi' => $modelDataPrediksi,
    ]) ?>

</div>
