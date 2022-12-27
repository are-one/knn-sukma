<?php

use yii\helpers\Html;

?>
<!-- Navigation -->
    <nav
        class="top1 navbar navbar-default navbar-static-top"
        role="navigation"
        style="margin-bottom: 0"
      >
        <div class="navbar-header">
          <button
            type="button"
            class="navbar-toggle"
            data-toggle="collapse"
            data-target=".navbar-collapse"
          >
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.html"><?= Yii::$app->name ?></a>
        </div>
        <!-- /.navbar-header -->
        <ul class="nav navbar-nav navbar-right">

          <li class="dropdown">
            <a href="#" class="dropdown-toggle avatar" data-toggle="dropdown"
              ><i class="fa fa-user"></i><span class="badge"> </span></a
            >
            <ul class="dropdown-menu">
              <li class="m_2">
                <a href="#"><i class="fa fa-user"></i> <?= Yii::$app->user->identity->username ?></a>
              </li>
              <li class="m_2">
                <?= Html::a('<i class="fa fa-lock"></i> Logout',['site/logout'],['data' => ['method' => 'post']]) ?>
              </li>
            </ul>
          </li>
        </ul>
        <!-- <form class="navbar-form navbar-right">
          <input
            type="text"
            class="form-control"
            value="Search..."
            onfocus="this.value = '';"
            onblur="if (this.value == '') {this.value = 'Search...';}"
          />
        </form> -->
        <div class="navbar-default sidebar" role="navigation">
          <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu">
              <li>
                <?= Html::a('<i class="fa fa-dashboard fa-fw nav_icon"></i> Dashboard', ['site/index']) ?>
              </li>
              <li>
                <?= Html::a('<i class="fa fa-laptop nav_icon"></i> Jenis Barang', ['jenis-barang/index']) ?>
              </li>
              <li>
                <?= Html::a('<i class="fa fa-calendar nav_icon"></i> Tahun-Bulan', ['tahun-bulan/index']) ?>
              </li>
              <li>
                <?= Html::a('<i class="fa fa-tasks nav_icon"></i> Data Training', ['penjualan/index']) ?>
              </li>
              <li>
                <?= Html::a('<i class="fa fa-spinner nav_icon"></i> Data Testing', ['prediksi-penjualan/index']) ?>
              </li>
              <li class="nav-item"><?=
                    Html::beginForm(['/site/logout'])
                    . Html::submitButton(
                        'Logout (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'nav-link btn btn-link logout']
                    )
                    . Html::endForm()
                    ?>
              </li>
            </ul>
          </div>
          <!-- /.sidebar-collapse -->
        </div>
        <!-- /.navbar-static-side -->
      </nav>