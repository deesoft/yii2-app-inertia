<?php

namespace app\helpers;

use app\models\AuthItem;
use ReflectionClass;
use Yii;
use yii\base\Controller;
use yii\base\Module;

/**
 * Description of Route
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Route
{
    private static $_routes;
    private static $_treeRoutes;

    /**
     * Get list of application routes
     * @return array
     */
    public static function getAppRoutes()
    {
        self::init();
        return self::$_routes;
    }

    /**
     * Get list of application routes
     * @return array
     */
    public static function getTreeRoutes()
    {
        self::init();
        return self::$_treeRoutes;
    }

    /**
     * Init routes
     */
    protected static function init()
    {
        if (self::$_routes === null) {
            self::$_routes = [];
            self::$_treeRoutes = static::getRouteRecursive(Yii::$app, self::$_routes);
        }
    }
    /**
     * Get route(s) recursive
     * @param Module $module
     * @param array $result
     */
    protected static function getRouteRecursive($module, &$result)
    {
        if (in_array($module->uniqueId, ['gii', 'debug'], true)) {
            return[];
        }
        $mId = '/' . ltrim($module->uniqueId . '/', '/');
        $items = [];
        foreach ($module->getModules() as $id => $child) {
            if (($child = $module->getModule($id)) !== null) {
                $children = static::getRouteRecursive($child, $result);
                if ($children) {
                    $items[] = [
                        'id' => $child->uniqueId,
                        'title' => $child->id,
                        'children' => $children,
                    ];
                }
            }
        }

        foreach ($module->controllerMap as $id => $type) {
            $children = static::getControllerActions($type, $id, $module, $result);
            if ($children) {
                $items[] = [
                    'id' => $mId . $id,
                    'title' => $id,
                    'children' => $children,
                ];
            }
        }

        $namespace = trim($module->controllerNamespace, '\\') . '\\';
        $children = static::getControllerFiles($module, $namespace, '', $result);
        if ($children) {
            foreach ($children as $child) {
                $items[] = $child;
            }
        }
        $all = '/' . ltrim($module->uniqueId . '/*', '/');
        $result[$all] = $all;
        if (count($items)) {
            $items[] = [
                'id' => $all,
                'title' => '*',
            ];
        }
        return $items;
    }

    /**
     * Get list controller under module
     * @param Module $module
     * @param string $namespace
     * @param string $prefix
     * @param mixed $result
     * @return mixed
     */
    protected static function getControllerFiles($module, $namespace, $prefix, &$result)
    {
        $path = Yii::getAlias('@' . str_replace('\\', '/', $namespace), false);

        if (!is_dir($path)) {
            return[];
        }
        $items = [];
        foreach (scandir($path) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir($path . '/' . $file) && preg_match('%^[a-z0-9_/]+$%i', $file . '/')) {
                $n1 = count($result);
                $children = static::getControllerFiles($module, $namespace . $file . '\\', $prefix . $file . '/', $result);
                if (count($result) > $n1) {
                    $id = '/' . ltrim($module->uniqueId . '/', '/') . $prefix . $file . '/*';
                    $result[$id] = $id;
                    $children[] = [
                        'id' => $id,
                        'title' => '*',
                    ];
                }
                if ($children) {
                    $items[] = [
                        'id' => '/' . ltrim($module->uniqueId . '/', '/') . $prefix . $file,
                        'title' => $file,
                        'children' => $children,
                    ];
                }
            } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                $baseName = substr(basename($file), 0, -14);
                $name = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $baseName));
                $id = ltrim(str_replace(' ', '-', $name), '-');
                $className = $namespace . $baseName . 'Controller';
                if (strpos($className, '-') === false && class_exists($className) && is_subclass_of($className, 'yii\base\Controller')) {
                    $children = static::getControllerActions($className, $prefix . $id, $module, $result);
                    $items[] = [
                        'id' => $prefix . $id,
                        'title' => $id,
                        'children' => $children,
                    ];
                }
            }
        }
        return $items;
    }

    /**
     * Get list action of controller
     * @param mixed $type
     * @param string $id
     * @param Module $module
     * @param string $result
     */
    protected static function getControllerActions($type, $id, $module, &$result)
    {
        /* @var $controller Controller */
        $controller = Yii::createObject($type, [$id, $module]);
        $items = static::getActionRoutes($controller, $result);
        $all = "/{$controller->uniqueId}/*";
        $result[$all] = $all;
        $items[] = [
            'id' => $all,
            'title' => '*',
        ];
        return $items;
    }

    /**
     * Get route of action
     * @param Controller $controller
     * @param array $result all controller action.
     */
    protected static function getActionRoutes($controller, &$result)
    {
        $items = [];
        $prefix = '/' . $controller->uniqueId . '/';
        foreach ($controller->actions() as $id => $value) {
            $result[$prefix . $id] = $prefix . $id;
            $items[] = [
                'id' => $prefix . $id,
                'title' => $id,
            ];
        }
        $class = new ReflectionClass($controller);
        foreach ($class->getMethods() as $method) {
            $name = $method->getName();
            if ($method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions') {
                $name = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', substr($name, 6)));
                $id = $prefix . ltrim(str_replace(' ', '-', $name), '-');
                $result[$id] = $id;
                $items[] = [
                    'id' => $id,
                    'title' => ltrim(str_replace(' ', '-', $name), '-'),
                ];
            }
        }
        return $items;
    }

    public static function refreshRoute()
    {
        $routes = self::getAppRoutes();
        $dbRoutes = AuthItem::find()->select(['name'])
            ->where([
                'AND',
                ['type' => 2],
                "[[name]] LIKE '/%'",
            ])
            ->column();

        $deleted = [];
        foreach ($dbRoutes as $name) {
            if (isset($routes[$name])) {
                unset($routes[$name]);
            } else {
                $deleted[] = $name;
            }
        }

        $time = time();
        $rows = [];
        foreach ($routes as $name) {
            $rows[] = [$name, 2, $time, $time];
        }
        $columns = ['name', 'type', 'created_at', 'updated_at'];
        if (count($rows)) {
            AuthItem::getDb()
                ->createCommand()
                ->batchInsert(AuthItem::tableName(), $columns, $rows)
                ->execute();
        }
        if (count($deleted)) {
            AuthItem::deleteAll(['name' => $deleted]);
        }
    }
}
