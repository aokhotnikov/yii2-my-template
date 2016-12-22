<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\AccountActivation;
use app\models\LoginForm;
use app\models\ResetPasswordForm;
use app\models\SendEmailForm;
use app\models\SignupForm;
use app\models\User;
use yii\base\InvalidParamException;
use yii\bootstrap\Html;
use yii\web\BadRequestHttpException;
use Yii;

class UserController extends BehaviorsController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Signup action.
     *
     * @return string
     */
    public function actionSignup()
    {
        $emailActivation = Yii::$app->params['emailActivation'];
        $model = $emailActivation ? new SignupForm(['scenario' => 'emailActivation']) : new SignupForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($user = $model->signup()) {
                if ($user->status === User::STATUS_ACTIVATED) {
                    if (Yii::$app->getUser()->login($user)) {
                        return $this->goHome();
                    }
                } else {
                    if ($model->sendActivationEmail($user))
                        Helpers::showNotify('Письмо с активацией отправлено на емайл <strong>' . Html::encode($user->email) . '</strong> (проверьте папку спам).', 'info', true, 'glyphicon-envelope');
                    else {
                        Helpers::showNotify('Ошибка. Письмо не отправлено.', 'danger', true, 'glyphicon-warning-sign');
                        Yii::error('Ошибка отправки письма.');  // Пишем ошибку в журнал
                    }
                    return $this->refresh();
                }
            } else {
                Helpers::showNotify('Возникла ошибка при регистрации пользователя', 'danger', true, 'glyphicon-warning-sign');
                Yii::error('Ошибка при регистрации');
                return $this->refresh();
            }
        }
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionActivateAccount($key)
    {
        try {
            $user = new AccountActivation($key);
        }
        catch(InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user->activateAccount())
            Helpers::showNotify('Активация аккаунта прошла успешно!', 'success', true);
        else {
            Helpers::showNotify('Ошибка активации', 'danger', true, 'glyphicon-warning-sign');
            Yii::error('Ошибка при активации.');  // Пишем ошибку в журнал
        }
        return $this->redirect(['/user/login']);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSendEmail()
    {
        $model = new SendEmailForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if($model->sendEmail()) {
                    Helpers::showNotify('Проверьте ваш Email', 'info', true, 'glyphicon-envelope');
                    return $this->goHome();
                } else {
                    Helpers::showNotify('Нельзя сбросить пароль', 'warning');
                }
            }
        }
        return $this->render('sendEmail', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($key)
    {
        try {
            $model = new ResetPasswordForm($key);
        }
        catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->resetPassword()) {
                Helpers::showNotify('Пароль изменен');
                return $this->redirect(['/user/login']);
            }
        }
        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

}