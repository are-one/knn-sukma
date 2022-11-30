<?php

use app\models\JenisBarang;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\search\JenisBarangSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Jenis Barang';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jenis-barang-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <hr>
    <p>
        <?= Html::a('Tambah Jenis Barang', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'jenis_barang',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, JenisBarang $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
