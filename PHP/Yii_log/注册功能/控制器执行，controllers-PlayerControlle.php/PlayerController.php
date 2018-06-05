<?php
namespace app\controllers;
/**
* 
*/
use Yii;
use yii\web\Controller;
use app\models\Player;
use app\models\RegisterForm;
class PlayerController extends Controller
{
	public function actionIndex()
	{
		$model = new User();
		$this->render('index',['model'=>$model]);
	}

	public function actionRegister()
	{
		$this->layout = 'register.php';//去布局，用自定义布局，或重新写样式代码
		$model = new RegisterForm();
		if ($model->load(Yii::$app->request->post())) {
			if ($model->register()) {

				return $this->render('login');
			}
		}

		return $this->render('register',['model'=>$model]);
	}

	public function actionLogin()
	{

		return $this->render('login');
	}
}