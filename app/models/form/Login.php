<?php

namespace app\models\form;

use app\models\Auth;
use Yii;
use yii\base\Model;
use function env;

/**
 * LoginForm is the model behind the login form.
 *
 * @property Auth|null $user This property is read-only.
 *
 */
class Login extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    /**
     *
     * @return boolean|string
     */
    public function apiLogin()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $payload = [
                'sub' => $user->id,
                'iat' => time(),
            ];
            $ttl = env('JWT_TTL', 0);
            if ($ttl > 0) {
                $payload['exp'] = time() + 60 * $ttl;
            }
            Yii::$app->user->login($user);
            return Yii::$app->jwt->encode($payload);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return Auth|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Auth::findByUsername($this->username);
        }

        return $this->_user;
    }
}
