<?php

namespace app\controllers;

use app\models\File;
use app\models\form\ChangePassword;
use app\models\form\Login;
use app\models\form\PasswordResetRequest;
use app\models\form\ResetPassword;
use app\models\form\UserSignup;
use dee\inertia\Inertia;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;

/**
 * 
 *
 */
class AuthController extends Controller
{

    /**
     * {@inheritDoc}
     */
    public function verbs()
    {
        return [
            'logout' => ['POST'],
            'edit-avatar' => ['POST'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function accessControls()
    {
        return [
            'logout' => ['@'],
            'profile' => ['@'],
            'change-password' => ['@'],
            'edit-avatar' => ['@'],
        ];
    }

    public function actionProfile()
    {
        return Inertia::render('auth/profile', ['user' => \Yii::$app->user->identity]);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->getIsGuest()) {
            return $this->goHome();
        }
        $model = new Login();
        if ($model->load($this->request->post(), '') && $model->login()) {
            $url = Yii::$app->user->getReturnUrl(['/site/index']);
            return Inertia::location($url);
        }
        return Inertia::render('auth/login', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return Inertia::location(['/site/index']);
    }

    public function actionChangePassword()
    {
        $model = new ChangePassword();
        if ($model->load($this->request->post(), '') && $model->change()) {
            return $this->redirect(['/site/index']);
        }
        return Inertia::render('auth/change-password', [
                'model' => $model,
        ]);
    }

    public function actionRegister()
    {
        $model = new UserSignup();
        if ($model->load($this->request->post(), '') && $model->signup()) {
            return $this->redirect(['/auth/login']);
        }
        return Inertia::render('auth/register', [
                'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        $model = new ResetPassword($token);
        if ($model->load($this->request->post(), '') && $model->resetPassword()) {
            return $this->redirect(['/auth/login']);
        }
        return Inertia::render('auth/reset-password', [
                'model' => $model,
        ]);
    }

    public function actionForgotPassword()
    {
        $model = new PasswordResetRequest();
        if ($model->load($this->request->post(), '') && $model->sendEmail()) {
            return $this->redirect(['/site/index']);
        }
        return Inertia::render('auth/forgot-password', [
                'model' => $model,
        ]);
    }

    public function actionEditAvatar()
    {
        $this->response->format = 'json';
        Yii::$app->getRequest()->getBodyParams();
        $file = UploadedFile::getInstanceByName('file');
        $model = File::store($file);
        if ($model->hasErrors()) {
            $this->response->setStatusCode(422, 'Data Validation Failed.');
            return $model->firstErrors;
        }
        $user = Yii::$app->user->identity;
        $user->avatar = $model->id;
        $user->save();
        return $user->toArray();
    }
}
