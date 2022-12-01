<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['tag' => false]
        ],
    ]); ?>

        <?= $form->field($model, 'username')->textInput(['class'=> false,'autofocus' => true, 'placeholder' => 'Username'])->label(false) ?>

        <?= $form->field($model, 'password')->passwordInput(['class'=> false, 'autofocus' => false, 'placeholder' => 'Password'])->label(false) ?>

        <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<div class=\"offset-lg-1 col-lg-3 custom-control custom-checkbox\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
        ]) ?>

        <div class="submit">
            <?= Html::submitInput('Login', ['name' => 'login-button', 'onclick' => 'myFunction()']) ?>
        </div>

		<!-- <ul class="new">
			<li class="new_left"><p><a href="#">Forgot Password ?</a></p></li>
			<li class="new_right"><p class="sign">New here ?<a href="register.html"> Sign Up</a></p></li>
			<div class="clearfix"></div>
		</ul> -->

<?php ActiveForm::end(); ?>
