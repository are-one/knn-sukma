<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TahunBulan $model */

$this->title = 'Update Tahun Bulan: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tahun Bulans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tahun-bulan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
