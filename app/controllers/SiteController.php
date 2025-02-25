<?php

namespace app\controllers;

use app\classes\Controller;
use app\models\form\Login;
use dee\inertia\Inertia;
use Yii;

/**
 * 
 *
 */
class SiteController extends Controller
{

    /**
     * {@inheritDoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function verbs()
    {
        return [
            'logout' => ['POST'],
        ];
    }

    protected function accessControls(): array
    {
        return [
            'logout' => ['@'],
        ];
    }

    public function actionIndex()
    {
        return Inertia::render('site/index');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->getIsGuest()) {
            return $this->goHome();
        }
        $model = new Login();
        if ($model->load($this->request->post(), '') && $model->login()) {
            $url = Yii::$app->user->getReturnUrl(['index']);
            return Inertia::location($url);
        }
        return Inertia::render('site/login', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return Inertia::location(['index']);
    }

    public function actionAbout()
    {
        return Inertia::render('site/about');
    }
}
