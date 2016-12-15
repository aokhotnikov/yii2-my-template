<?php

namespace app\controllers;

use Yii;

class AdminController extends BehaviorsController
{

    public function actionIndex()
    {
        return $this->render('index');
    }
}