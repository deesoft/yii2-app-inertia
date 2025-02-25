<?php

namespace app\models\form;

use app\helpers\Tools;
use app\models\Auth;
use yii\base\Model;

/**
 * Signup form
 */
class UserSignup extends Model
{
    public $username;
    public $email;
    public $phone;
    public $password;
    public $fullname;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        $class = Auth::class;
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => $class, 'message' => 'This email address has already been taken.'],
            ['username', 'default', 'value' => function () {
                if (preg_match('/^([^@]+)/', $this->email, $matched)) {
                    return $matched[1];
                }
            }],
            ['username', 'unique', 'targetClass' => $class, 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['phone', 'normalizePhone'],

            ['fullname', 'string', 'max' => 255],
            ['fullname', 'default', 'value' => function () {
                return $this->username;
            }]
        ];
    }

    /**
     * Signs user up.
     * @return Auth|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new Auth();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->phone = $this->phone;
            $user->fullname = $this->fullname;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save()) {
                return $user;
            }
        }
        return null;
    }

    public function normalizePhone($attribute)
    {
        $value = $this->$attribute;
        if ($value) {
            $phone = Tools::normalizePhone($value);
            if ($phone === false) {
                $this->addError($attribute, "Invalid phone number '{$value}'");
            } else {
                $this->$attribute = $phone;
            }
        }
    }
}
