<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\helpers\VarDumper;

class BehaviorsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'controllers' => ['user'],
                        'actions' => ['login', 'signup', 'send-email', 'reset-password'],
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => false,
                        'controllers' => ['user'],
                        'actions' => ['login', 'signup'],
                        'roles' => ['@'],
                        'denyCallback' => function($rule, $action) {    // вызывается только, когда доступ запрещён('allow' => false)
                            //VarDumper::dump($action, 10, true);       // for debug
                            if (Yii::$app->user->identity->isAdmin) {
                                return $this->redirect('admin');
                            }
                            return $this->goHome();
                        },
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['user'],
                        'actions' => ['logout'],
                        'verbs' => ['POST'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['user'],
                        'actions' => ['index'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['admin'],
                        'actions' => ['index'],
                        'matchCallback' => function ($rule, $action) {
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin;
                        }
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['site'],
                        //'actions' => ['index', 'error', 'contact', 'captcha'],
                    ],
                    /*
                    [
                        'allow' => true,
                        'controllers' => ['admin'],
                        'actions' => ['index'],
                        //'ips' => ['127.1.*'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->isAdmin;
                        }
                    ],
                    */
                ],
            ],
        ];
    }
}