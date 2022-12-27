<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // 'css/site.css',
        'template/css/bootstrap.min.css',
        'template/css/style.css',
        'template/css/lines.css',
        'template/css/font-awesome.css', 
        'http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900',
        'template/css/custom.css',
    ];
    public $js = [
        // 'template/js/jquery.min.js',
        // 'template/js/bootstrap.min.js',
        'template/js/metisMenu.min.js',
        'template/js/custom.js',
        'template/js/d3.v3.js',
        'template/js/rickshaw.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        // 'yii\bootstrap\BootstrapAsset',
        // 'app\assets\SecondAppAsset'
    ];
}
