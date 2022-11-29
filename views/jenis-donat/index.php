<?php

use app\models\JenisDonat;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\search\JenisDonatSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Jenis Donat';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jenis-donat-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <hr>
    <p>
        <?= Html::a('Tambah Jenis Donat', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'jenis_donat',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, JenisDonat $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
