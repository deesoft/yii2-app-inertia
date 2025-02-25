<?php

namespace app\helpers;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\User;

/**
 * Description of Access
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Access
{
    public static $allowedGuestRoutes = [];
    public static $allowedAuthtRoutes = [];
    private static $_assignedRoutes = [];

    /**
     * @param int $userId
     * @return string[]
     */
    protected static function getAssignedRoutes($userId)
    {
        if (isset(self::$_assignedRoutes[$userId])) {
            return self::$_assignedRoutes[$userId];
        }
        $routes = array_keys(Yii::$app->authManager->getPermissionsByUser($userId));
        return self::$_assignedRoutes[$userId] = array_combine($routes, $routes);
    }

    /**
     *
     * @param string $route
     * @param User $user
     * @return bool
     */
    public static function checkRoute($route, $user = null)
    {
        if ($user === null) {
            $user = Yii::$app->getUser();
        }
        $r = static::normalizeRoute($route);
        $allowed = $user->id ? static::$allowedAuthtRoutes : static::$allowedGuestRoutes;
        $allowed = array_combine($allowed, $allowed);
        $routes = array_merge(static::getAssignedRoutes($user->id), $allowed);
        if (isset($routes['/*'])) {
            return true;
        }
        if (isset($routes[$r])) {
            return true;
        }
        while (($pos = strrpos($r, '/')) > 0) {
            $r = substr($r, 0, $pos);
            if (isset($routes[$r . '/*'])) {
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
    protected static function normalizeRoute($route)
    {
        if ($route === '') {
            $normalized = '/' . Yii::$app->controller->getRoute();
        } elseif (strncmp($route, '/', 1) === 0) {
            $normalized = $route;
        } elseif (strpos($route, '/') === false) {
            $normalized = '/' . Yii::$app->controller->getUniqueId() . '/' . $route;
        } elseif (($mid = Yii::$app->controller->module->getUniqueId()) !== '') {
            $normalized = '/' . $mid . '/' . $route;
        } else {
            $normalized = '/' . $route;
        }
        return $normalized;
    }

    /**
     *
     * @return array
     */
    public static function getAssignedMenu()
    {
        Route::refreshRoute();
        $allMenu = require(Yii::getAlias('@app/config/menu.php'));
        $user = Yii::$app->getUser();
        $result = static::filterMenuRecursive($allMenu, $user);
        return $result;
    }

    /**
     *
     * @param array $items
     * @param User $user
     * @return array
     */
    protected static function filterMenuRecursive($items, $user)
    {
        $result = [];
        foreach ($items as $item) {
            $allow = false;
            if (!empty($item['route'])) {
                $route = '/' . trim($item['route'], '/');
                $allow = static::checkRoute($route, $user);
            }
            $menu = [
                'title' => $item['title'],
                'icon' => $item['icon'] ?? null,
                'to' => isset($route) ? Url::to([$route]) : null,
            ];

            if (isset($item['children']) && is_array($item['children'])) {
                $subItems = self::filterMenuRecursive($item['children'], $user);
                if (count($subItems)) {
                    $allow = true;
                    $menu['children'] = $subItems;
                }
            }

            if ($allow) {
                $result[] = $menu;
            }
        }
        return $result;
    }
}
