<?php

namespace app\classes;

use app\helpers\Route;
use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\User;

/**
 * Description of AccessControl
 *
 * @property array|mixed $assignedMenu
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class AccessControl extends ActionFilter
{
    /**
     *
     * @var string[]
     */
    public $allowed = [];
    /**
     *
     * @var string|array
     */
    public $menus;
    /**
     * @var User User for check access.
     */
    private $_user;
    /**
     *
     * @var string[]
     */
    private $_assignedRoutes;

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
     *
     */
    protected function prepare()
    {
        if ($this->_assignedRoutes === null) {
            $this->_assignedRoutes = Yii::$app->authManager->getPermissionsByUser($this->getUser()->id);
            foreach ($this->allowed as $route) {
                $route = '/' . trim($route, '/');
                $this->_assignedRoutes[$route] = true;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $actionId = $action->getUniqueId();
        $user = $this->getUser();
        if ($this->checkRoute('/' . $actionId)) {
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
        return parent::isActive($action) && !$this->isSpecialRoute($action);
    }

    /**
     *
     * @param Action $action
     * @return boolean
     */
    protected function isSpecialRoute($action)
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
        return count($allowIds) && in_array($uniqueId, $allowIds, true);
    }

    /**
     *
     * @param string $route
     * @return bool 
     */
    public function checkRoute($route)
    {
        $this->prepare();
        $r = $this->normalizeRoute($route);

        if (isset($this->_assignedRoutes['/*'])) {
            return true;
        }
        if (isset($this->_assignedRoutes[$r])) {
            return true;
        }
        while (($pos = strrpos($r, '/')) > 0) {
            $r = substr($r, 0, $pos);
            if (isset($this->_assignedRoutes[$r . '/*'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Normalize route
     * @param  string  $route    Plain route string
     * @return string            Normalized route string
     */
    protected function normalizeRoute($route)
    {
        if ($route === '') {
            return'/' . Yii::$app->controller->getRoute();
        } elseif (strncmp($route, '/', 1) === 0) {
            return $route;
        } elseif (strpos($route, '/') === false) {
            return '/' . Yii::$app->controller->getUniqueId() . '/' . $route;
        } elseif (($mid = Yii::$app->controller->module->getUniqueId()) !== '') {
            return '/' . $mid . '/' . $route;
        } else {
            return '/' . $route;
        }
    }

    /**
     *
     * @return array
     */
    public function getAssignedMenu()
    {
        if ($this->menus) {
            Route::refreshRoute();
            $allMenu = is_string($this->menus) ? require(Yii::getAlias($this->menus)) : $this->menus;
            $result = $this->filterMenuRecursive($allMenu);
            return $result;
        }
        return [];
    }

    /**
     *
     * @param array $items
     * @return array
     */
    protected function filterMenuRecursive($items)
    {
        $result = [];
        foreach ($items as $item) {
            $menu = $item;
            unset($menu['items']);
            $allow = false;
            if (!empty($item['route'])) {
                $route = '/' . trim($item['route'], '/');
                $allow = $this->checkRoute($route);
                $menu['href'] = Url::to((array) $route);
            }

            if (isset($item['items']) && is_array($item['items'])) {
                $subItems = $this->filterMenuRecursive($item['items']);
                if (count($subItems)) {
                    $allow = true;
                    $menu['items'] = $subItems;
                }
            }

            if ($allow) {
                $result[] = $menu;
            }
        }
        return $result;
    }
}
