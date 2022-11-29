<?php

/** @var yii\web\View $this */

use yii\helpers\Url;

$this->title = 'KNN';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Selamat Datang!</h1>

        <p class="lead">Klasifikasi Penjualan Menggunakan Metode K-Nearest Neighbor.</p>

        <p><a class="btn btn-lg btn-success" href="<?= Url::to(['prediksi-penjualan/index']) ?>">Mulai Prediksi</a></p>
    </div>

</div>
