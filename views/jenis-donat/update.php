<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\JenisDonat $model */

$this->title = 'Update Jenis Donat: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Jenis Donats', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="jenis-donat-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
