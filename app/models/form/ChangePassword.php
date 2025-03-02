<?php

namespace app\models\form;

use app\models\Auth;
use Yii;
use yii\base\Model;

/**
 * Description of ChangePassword
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class ChangePassword extends Model
{
    public $password;
    public $new_password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'new_password'], 'required'],
            [['password'], 'validatePassword'],
            [['new_password'], 'string', 'min' => 6],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        /* @var $user Auth */
        $user = Yii::$app->user->identity;
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError('password', 'Incorrect old password.');
        }
    }

    /**
     * Change password.
     *
     * @return Auth|null the saved model or null if saving fails
     */
    public function change()
    {
        if ($this->validate()) {
            /* @var $user Auth */
            $user = Yii::$app->user->identity;
            $user->setPassword($this->new_password);
            $user->generateAuthKey();
            if ($user->save()) {
                return true;
            }
        }

        return false;
    }
}
