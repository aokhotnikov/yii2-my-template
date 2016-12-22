<?php
namespace app\models;

use Yii;
use yii\base\Model;


/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $repassword;
    public $status;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => 'app\models\User'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => 'app\models\User'],

            ['password', 'required'],
            ['password', 'string', 'min' => 3, 'max' => 255],

            ['repassword', 'compare', 'compareAttribute' => 'password'],

            ['status', 'default', 'value' => User::STATUS_ACTIVATED, 'on' => 'default'],
            ['status', 'in', 'range' =>[
                User::STATUS_NEW,
                User::STATUS_ACTIVATED
            ]],
            ['status', 'default', 'value' => User::STATUS_NEW, 'on' => 'emailActivation'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Никнейм',
            'email' => 'E-mail',
            'password' => 'Пароль',
            'repassword' => 'Подтвердите пароль',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        if($this->scenario === 'emailActivation') {
            $user->generateActivationToken();
        }
        $user->status = $this->status;

        //echo '<pre>';print_r($this);echo '</pre>';die;
        return $user->save() ? $user : null;
    }

    public function sendActivationEmail($user)
    {
        return Yii::$app->mailer->compose('activationEmail', ['user' => $user])
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name.' (отправлено роботом).'])
            ->setTo($this->email)
            ->setSubject('Активация для '.Yii::$app->name)
            ->send();
    }
}
