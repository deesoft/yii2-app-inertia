<?php

namespace app\classes;

use Yii;
use yii\db\BaseActiveRecord;
use yii\db\Query;
use yii\db\ActiveQuery;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\base\NotSupportedException;
use yii\base\InvalidConfigException;

/**
 * Description of QueryRecord
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
abstract class QueryRecord extends BaseActiveRecord
{

    /**
     * @return Query|string
     */
    abstract public static function query();

    /**
     * {@inheritdoc}
     */
    public function insert($runValidation = true, $attributes = null)
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }

    /**
     * @return ActiveQuery
     */
    public static function find()
    {
        $class = get_called_class();
        return (new ActiveQuery($class))
                ->from([Inflector::camel2id(StringHelper::basename($class), '_') => static::query()]);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDb()
    {
        return Yii::$app->getDb();
    }

    /**
     * {@inheritdoc}
     */
    public static function primaryKey()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        throw new InvalidConfigException(__METHOD__ . ' must be override');
    }
}
