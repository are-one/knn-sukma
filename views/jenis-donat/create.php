<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\JenisDonat $model */

$this->title = 'Tambah Jenis Donat';
$this->params['breadcrumbs'][] = ['label' => 'Jenis Donat', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jenis-donat-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
