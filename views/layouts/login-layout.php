<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\LoginAppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;

LoginAppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
</head>
<body id="login">
<?php $this->beginBody() ?>
    <div class="login-logo">
        <a href="index.html"><?= Html::img(Url::base()."/img/uho.png",['width' => 150]) ?><img src="/template/images/logo.png" alt=""/></a>
    </div>

    <h2 class="form-heading">login</h2>

    <div class="app-cam">
    <?= $content ?>

    </div>
    <div class="copy_layout login">
        <p>
            <div class="col-md-6 text-center text-md-start">&copy; KNN <?= date('Y') ?></div>
            <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
        </p>
    </div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
