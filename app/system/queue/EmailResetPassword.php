<?php

namespace app\system\queue;

use app\models\User;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use function env;

/**
 * Description of EmailResetPassword
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class EmailResetPassword extends BaseObject implements JobInterface
{
    public $userId;
    public $resetLink;

    //put your code here
    public function execute($queue)
    {
        $user = User::findOne(['id' => $this->userId]);
        if ($user) {
            $from = env('EMAIL_FROM', 'admin@example.com');
            Yii::$app->mailer->compose([
                    'html' => 'passwordResetToken-html',
                    'text' => 'passwordResetToken-text'], ['user' => $user, 'resetLink' => $this->resetLink])
                ->setFrom($from)
                ->setTo($user->email)
                ->setSubject('Password reset for ' . Yii::$app->name)
                ->send();
        }
    }
}
