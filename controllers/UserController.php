<?php
namespace app\controllers;

use app\models\LoginForm;
use app\models\SignupForm;
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
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            } else {
                Yii::$app->session->setFlash('error', 'Возникла ошибка при регистрации пользователя');
                Yii::error('Ошибка при регистрации');
                return $this->refresh();
            }
        }
        return $this->render('signup', [
            'model' => $model,
        ]);
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

}