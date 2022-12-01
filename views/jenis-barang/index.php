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

    <div class="bs-example1" data-example-id="contextual-table">
        <p>
            <?= Html::a('Tambah Jenis Barang', ['create'], ['class' => 'btn btn-success', 'style' => 'margin-bottom: 5px']) ?>
        </p>

        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                // 'id',
                [
                    'attribute' => 'jenis_barang',
                    'filterInputOptions' => [
                        'class' => 'form-control1'
                    ],
                ],
                [
                    'class' => ActionColumn::className(),
                    'urlCreator' => function ($action, JenisBarang $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    }
                ],
            ],
        ]); ?>
    </div>

</div>
