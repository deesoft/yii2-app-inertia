<?php

namespace app\commands;

use yii\base\InlineAction;
use yii\base\UnknownPropertyException;
use yii\console\Controller;

/**
 * Description of ExecController
 *
 * @property array $properties
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class ExecController extends Controller
{
    private $_actions = [];
    private $_vars = [];
    private $_descriptions = [];
    private $_properties = [];

    public function init()
    {
        parent::init();
        $dir = __DIR__ . '/cmds';
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..' || substr($file, -4) !== '.php') {
                continue;
            }
            $name = substr($file, 0, -4);
            if (substr($name, -6) === '-local') {
                $name = substr($name, 0, -6);
            }
            if (empty($name) || !preg_match('/^[a-zA-Z][a-zA-Z0-9\-\_]*$/', $name) || isset($this->_actions[$name])) {
                continue;
            }
            $this->_actions[$name] = [
                'class' => InlineAction::class,
                'actionMethod' => 'execFile',
            ];
            $content = file_get_contents($dir . '/' . $file);
            $this->getVars($name, $content);
        }
        closedir($handle);
    }

    public function actions()
    {
        return $this->_actions;
    }

    /**
     *
     */
    public function actionIndex()
    {
        echo implode("\n", array_keys($this->_actions)), "\n";
    }

    public function execFile()
    {
        $__data__ = $this->getVarValues($this->action->id);
        $params = func_get_args();
        extract($__data__);
        if (is_file(__DIR__ . "/cmds/{$this->action->id}.php")) {
            return include __DIR__ . "/cmds/{$this->action->id}.php";
        } elseif (is_file(__DIR__ . "/cmds/{$this->action->id}-local.php")) {
            return include __DIR__ . "/cmds/{$this->action->id}-local.php";
        }
    }

    protected function getVarValues($id)
    {
        $result = [];
        if (isset($this->_vars[$id])) {
            foreach (array_keys($this->_vars[$id]) as $key) {
                $result[$key] = $this->{$key};
            }
        }
        return $result;
    }

    /**
     *
     * @param type $name
     * @param type $content
     * @return type
     */
    protected function getVars($name, $content)
    {
        $tokens = token_get_all($content);
        $description = '';
        $result = [];
        $regexVar = '/^@var\s+(\w+)(\s*\[\])?\s+\$(\w+)(\s+.*)?/s';
        foreach ($tokens as $token) {
            if (is_array($token) && $token[0] == T_DOC_COMMENT && preg_match('/^\/\*\*\s*(.+)\*\//s', $token[1], $matches)) {
                $doc = trim($matches[1]);
                if (preg_match($regexVar, $doc, $matches)) { // is var
                    if (in_array($matches[1], ['int', 'bool', 'string', 'float', 'double', 'boolean', 'integer'])) {
                        $comment = isset($matches[4]) ? strtr(trim(preg_replace('/^\s*\**([ \t])?/m', '', trim($matches[4], '/'))), "\r", '')
                                : '';
                        $result[$matches[3]] = [
                            'type' => $matches[1],
                            'default' => null,
                            'comment' => $comment,
                            'is_array' => !empty($matches[2]),
                        ];
                    }
                } elseif (!$description) { // deskripsi
                    $description = strtr(trim(preg_replace('/^\s*\**([ \t])?/m', '', trim($doc, '/'))), "\r", '');
                }
            }
        }
        $this->_vars[$name] = $result;
        $this->_descriptions[$name] = $description;
    }

    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch (UnknownPropertyException $exc) {
            return $this->_properties[$name] ?? null;
        }
    }

    public function __set($name, $value)
    {
        try {
            return parent::__set($name, $value);
        } catch (UnknownPropertyException $exc) {
            return $this->_properties[$name] = $value;
        }
    }

    public function options($actionID): array
    {
        $options = parent::options($actionID);
        if (!empty($this->_vars[$actionID])) {
            return array_merge($options, array_keys($this->_vars[$actionID]));
        }
        return $options;
    }

    public function runAction($id, $params = [])
    {
        if (isset($this->_vars[$id])) {
            foreach ($this->_vars[$id] as $name => $value) {
                if (!isset($this->_properties[$name])) {
                    $this->_properties[$name] = $value['is_array'] ? [] : null;
                }
            }
        }
        return parent::runAction($id, $params);
    }

    /**
     * {@inheritDoc}
     */
    public function getActionOptionsHelp($action)
    {
        $options = parent::getActionOptionsHelp($action);
        if (isset($this->_vars[$action->id])) {
            foreach ($this->_vars[$action->id] as $name => $opt) {
                $name = \yii\helpers\Inflector::camel2id($name, '-', true);
                $options[$name] = $opt;
            }
        }
        return $options;
    }

    /**
     * {@inheritDoc}
     */
    public function getActionHelp($action)
    {
        if (isset($this->_descriptions[$action->id])) {
            return $this->_descriptions[$action->id];
        }
        return parent::getActionHelp($action);
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->_properties;
    }
}
