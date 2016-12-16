<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SendEmailForm extends Model
{
    public $email;
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => User::className(),
                'filter' => [
                    'status' => User::STATUS_ACTIVATED
                ],
                'message' => 'Данный емайл не зарегистрирован.'
            ],
        ];
    }
    public function attributeLabels()
    {
        return [
            'email' => 'Емайл'
        ];
    }
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne(
            [
                'status' => User::STATUS_ACTIVATED,
                'email' => $this->email
            ]
        );
        if ($user) {
            $user->generatePasswordResetToken();
            if ($user->save()) {
                return Yii::$app->mailer->compose('resetPassword', ['user' => $user])
                    ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name . ' (отправлено роботом)'])
                    ->setTo($this->email)
                    ->setSubject('Сброс пароля для ' . Yii::$app->name)
                    ->send();
            }
        }
        return false;
    }
}