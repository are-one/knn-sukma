<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\JenisBarang $model */

$this->title = 'Tambah Jenis Barang';
$this->params['breadcrumbs'][] = ['label' => 'Jenis Barang', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jenis-barang-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
