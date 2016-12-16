<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;

class ResetPasswordForm extends Model
{
    public $password;
    private $_user;

    public function rules()
    {
        return [
            ['password', 'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Пароль'
        ];
    }

    public function __construct($key, $config = [])
    {
        if (empty($key) || !is_string($key))
            throw new InvalidParamException('Ключ не может быть пустым.');
        $this->_user = User::findByPasswordResetToken($key);
        if(!$this->_user)
            throw new InvalidParamException('Не верный ключ.');
        parent::__construct($config);
    }

    public function resetPassword()
    {
        /* @var $user User */
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        return $user->save();
    }
}