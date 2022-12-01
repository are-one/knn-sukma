<?php

use app\models\TahunBulan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\search\TahunBulanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Tahun Bulan';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tahun-bulan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="bs-example1" data-example-id="contextual-table">

    <p>
        <?= Html::a('Tambah Tahun Bulan', ['create'], ['class' => 'btn btn-success', 'style' => 'margin-bottom: 5px']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            [
                'attribute' => 'bulan',
                'filterInputOptions' => [
                    'class' => 'form-control1'
                ],
            ],
            [
                'attribute' => 'tahun',
                'filterInputOptions' => [
                    'class' => 'form-control1'
                ],
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, TahunBulan $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    </div>

</div>
