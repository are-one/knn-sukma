<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\PrediksiPenjualan $model */

$this->title = 'Update Prediksi Penjualan: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Prediksi Penjualans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="prediksi-penjualan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
