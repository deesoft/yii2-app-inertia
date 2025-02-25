<?php

namespace app\models\form;

use App\Classes\Mailable;
use App\Models\Auth;
use App\Models\User;
use yii\base\Model;
use function config;

/**
 * Password reset request form
 */
class OtpRequest extends Model
{
    public $identity;

    /**
     *
     * @var Auth 
     */
    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['identity', 'filter', 'filter' => 'trim'],
            ['identity', 'required'],
            ['identity', 'checkIdentity'],
        ];
    }

    public function checkIdentity()
    {
        $this->_user = $user = Auth::findByIdentity($this->identity);
        if (!$user || !$user->active) {
            $this->addError('identity', "User '{$this->identity}' not found.");
        }
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendOtp()
    {
        if ($this->validate()) {
            /* @var $user Auth */
            $user = $this->_user;
            $methods = [];
            if ($user->email === $this->identity) {
                $methods = ['sendEmail'];
            } elseif ($user->phone === ($phone = User::normalizePhone($this->identity))) {
                $methods = ['sendPhone'];
            } elseif ($user->username === $this->identity) {
                $methods = ['sendEmail', 'sendPhone'];
            }
            if (!($code = static::checkOtp($user->otp))) {
                $expire = config('auth.otp_timeout', 15 * 60);
                $code = mt_rand(100000, 999999);
                $user->otp = $code . '_' . (time() + $expire);
            }

            if ($user->save(false)) {
                foreach ($methods as $method) {
                    $this->$method($user, $code);
                }
                return true;
            }
        }
        return false;
    }
    private $_messages = [];

    public function getMessage()
    {
        return $this->_messages;
    }

    /**
     *
     * @param Auth $user
     * @param string $code
     */
    protected function sendEmail($user, $code)
    {
        Mailable::compose('otp', [
                'name' => $user->fullname,
                'code' => $code,
            ])
            ->to($user->email)
            ->subject('Otp')
            ->queue();
        $this->_messages[] = "OTP send to '{$user->email}'";
    }

    /**
     *
     * @param Auth $user
     * @param string $code
     */
    protected function sendPhone($user, $code)
    {
        
    }

    public static function checkOtp($value)
    {
        if (empty($value)) {
            return false;
        }
        $parts = explode('_', $value);
        $timestamp = (int) end($parts);
        return ($timestamp >= time() && preg_match('/^\d+$/', $parts[0])) ? $parts[0] : false;
    }
}
