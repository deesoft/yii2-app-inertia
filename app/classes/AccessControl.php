<?php

namespace app\classes;

use app\helpers\Access;
use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\User;

/**
 * Description of AccessControl
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class AccessControl extends ActionFilter
{
    /**
     * @var User User for check access.
     */
    private $_user = 'user';

    /**
     * Get user
     * @return User
     */
    public function getUser()
    {
        if (!$this->_user instanceof User) {
            $this->_user = Yii::$app->user;
        }
        return $this->_user;
    }

    /**
     * Set user
     * @param User|string $user
     */
    public function setUser($user)
    {
        $this->_user = $user;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $actionId = $action->getUniqueId();
        $user = $this->getUser();
        if (Access::checkRoute('/' . $actionId, $user)) {
            return true;
        }
        $this->denyAccess($user);
    }

    /**
     * Denies the access of the user.
     * The default implementation will redirect the user to the login page if he is a guest;
     * if the user is already logged, a 403 HTTP exception will be thrown.
     * @param  User $user the current user
     * @throws ForbiddenHttpException if the user is already logged in.
     */
    protected function denyAccess($user)
    {
        if ($user->getIsGuest()) {
            if ($user->enableSession && $user->loginUrl) {
                $user->loginRequired();
            } else {
                throw new UnauthorizedHttpException();
            }
        } else {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }

    /**
     * @inheritdoc
     */
    protected function isActive($action)
    {
        return parent::isActive($action) && $this->allowSpecialRoute($action);
    }

    /**
     *
     * @param Action $action
     * @return boolean
     */
    protected function allowSpecialRoute($action)
    {
        $user = $this->getUser();
        $allowIds = [Yii::$app->getErrorHandler()->errorAction];
        if (is_array($user->loginUrl) && isset($user->loginUrl[0])) {
            $allowIds[] = trim($user->loginUrl[0], '/');
        } elseif (is_string($user->loginUrl)) {
            $allowIds[] = trim($user->loginUrl, '/');
        }

        $uniqueId = $action->getUniqueId();
        $allowIds = array_filter($allowIds);
        if (count($allowIds) && in_array($uniqueId, $allowIds, true)) {
            return false;
        }

        return true;
    }
}
