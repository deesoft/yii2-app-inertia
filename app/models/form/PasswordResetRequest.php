<?php

namespace app\models\form;

use app\models\Auth;
use app\system\queue\EmailResetPassword;
use Yii;
use yii\base\Model;
use yii\helpers\Url;

/**
 * Password reset request form
 */
class PasswordResetRequest extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => Auth::class,
                'filter' => ['active' => true],
                'message' => 'There is no user with such email.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user Auth */
        $user = Auth::findOne(['active' => true, 'email' => $this->email,]);

        if ($user) {
            if (!Auth::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
            }

            if ($user->save()) {
                $resetLink = Url::to(['/auth/reset-password', 'token' => $user->password_reset_token], true);
                return Yii::$app->queue->push(new EmailResetPassword([
                    'resetLink' => $resetLink,
                    'userId' => $user->id,
                ]));
            }
        }

        return false;
    }
}
