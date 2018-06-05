<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "player".
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $phone
 * @property string $password
 * @property string $register_time
 */
class Player extends \yii\db\ActiveRecord
{
    public $password2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'player';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'phone', 'password'], 'required'],
            [['register_time'], 'safe'],
            [['username', 'email', 'password'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
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
