<?php
namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

class LoginAppAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';
 
    public $css = [
		'template/css/bootstrap.min.css',
		'template/css/style.css',
		'template/css/font-awesome.css',
		'http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900',
	];
 
	public $js =  [
       'template/js/jquery.min.js',
	   'template/js/bootstrap.min.js',
	];

	public $depends = [];
}

?>