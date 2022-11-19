<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TahunBulan $model */

$this->title = 'Tambah Tahun Bulan';
$this->params['breadcrumbs'][] = ['label' => 'Tahun Bulan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tahun-bulan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
