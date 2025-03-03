<?php

namespace app\controllers;

use app\classes\Controller;
use dee\inertia\Inertia;

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

    public function actionIndex()
    {
        return Inertia::render('site/index');
    }

    public function actionAbout()
    {
        return Inertia::render('site/about');
    }
}
