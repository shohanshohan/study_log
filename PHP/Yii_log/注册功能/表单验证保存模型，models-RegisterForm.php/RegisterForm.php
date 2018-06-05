<?php
namespace app\models;
use yii\base\Model;

/**
* 注册表单验证和保存
*/
class RegisterForm extends Model
{
	public $username;
	public $email;
	public $phone;
	public $password;
	public $gender;
	public $password2;

	//验证规则
	public function rules()
	{
		return [
			['username','trim'], //去空格
			['username','required','message'=>'用户名不能为空'], //必须
			//唯一性验证数据,提示信息
			['username','unique','targetClass'=>'\app\models\Player','message'=>'该用户名已存在！'],
			['username','string','min'=>2,'max'=>255],//字符串格式，长度规定2-255个字符

			['email','trim'],
			['email','required','message'=>'邮箱不能为空'],
			['email','email','message'=>'邮箱格式不正确'], //邮箱格式
			['email','string','max'=>255], //邮箱长度不大于255个字符
			['email','unique','targetClass'=>'\app\models\Player','message'=>'该邮箱已注册过了！'],

			['gender','required'],


			['password','required','message'=>'密码不能为空'],
			['password','string','min'=>6,'message'=>'密码长度最少6个字符'], //最少6个字符
			['password2','required','message'=>'请确认密码'],
		    ['password2', 'compare','compareAttribute'=>'password','message' => '两次密码输入不一致'],

			['phone','trim'],
			['phone','match','pattern'=>'/^[1][345678][0-9]{9}$/','message'=>'手机号码格式不对'], //手机号码验证
			['phone','unique','targetClass'=>'\app\models\Player','message'=>'该手机号码已用过了！'],
		];
	}

	//验证并保存
	public function register()
	{
		date_default_timezone_set('Asia/shanghai');
		if (!$this->validate()) {
			return null;
		}
		$player = new Player();
		$player->username = $this->username;
		$player->email = $this->email;
		$player->gender = $this->gender;
		$player->password = password_hash($this->password,PASSWORD_DEFAULT);
		$player->phone = $this->phone;
		$player->register_time = date('Y-m-d H:i:s');

		return $player->save() ? true : false;
	}

	//属性字段命名
	public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'email' => '邮箱',
            'gender' => '性别',
            'phone' => '联系电话',
            'password' => '密码',
            'password2' => '确认密码',
        ];
    }
}