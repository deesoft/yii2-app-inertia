<?php

namespace app\models\form;

use app\models\Auth;
use yii\base\InvalidParamException;
use yii\base\Model;

/**
 * Password reset form
 */
class ResetPassword extends Model
{
    public $password;
    public $retype_password;
    /**
     * @var Auth
     */
    private $_user;

    /**
     * Creates a form model given a token.
     *
     * @param  string $token
     * @param  array $config name-value pairs that will be used to initialize the object properties
     * @throws InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Password reset token cannot be blank.');
        }
        // check token
        if (Auth::isPasswordResetTokenValid($token)) {
            $this->_user = Auth::findByPasswordResetToken($token);
        }
        if (!$this->_user) {
            throw new InvalidParamException('Wrong password reset token.');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'retype_password'], 'required'],
            ['password', 'string', 'min' => 6],
            ['retype_password', 'compare', 'compareAttribute' => 'password']
        ];
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function resetPassword()
    {
        if ($this->validate()) {
            $user = $this->_user;
            $user->password = bcrypt($this->password);

            return $user->save(false);
        }
        return false;
    }
}
