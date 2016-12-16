<?php
/**
 * @var $user \app\models\User
 */
use yii\helpers\Html;
echo 'Привет '.Html::encode($user->username).'. ';
echo Html::a('Для смены пароля перейдите по этой ссылке.',
    Yii::$app->urlManager->createAbsoluteUrl(
        [
            '/user/reset-password',
            'key' => $user->password_reset_token
        ]
    ));