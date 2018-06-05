<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = '注册界面';
?>
<div class="site-signup">
	<h1><?= Html::encode($this->title) ?></h1>
	<span>有账号，去<a href="<?= Url::to(['player/login']);?>">登录</a></span><br><br>
	<div class="row">
		<div class="col-lg-5">
			<?php $form = ActiveForm::begin(['id'=>'form-signup']); ?>
				<?= $form->field($model,'username')->textInput(['autofocus'=>true]) ?>

				<?= $form->field($model,'email') ?>

				<?php $model->gender = '1'; ?>
				<?= $form->field($model,'gender')->radioList(['1'=>'男','0'=>'女']) ?>

				<?= $form->field($model,'phone') ?>
				
				<?= $form->field($model,'password')->passwordInput() ?>
				<?= $form->field($model,'password2')->passwordInput() ?>
				<div class="form-group">
					<?= Html::submitButton('点击注册',['class'=>'btn btn-primary','name'=>'signup-button']) ?>
				</div>
			<?php ActiveForm::end(); ?>
		</div>
	</div>
</div>