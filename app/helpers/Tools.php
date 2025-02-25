<?php

namespace app\helpers;

use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\validators\Validator;

/**
 * Description of Tools
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Tools
{
    public static $dateFormats = [
        'date' => 'Y-m-d',
        'time' => 'H:i:s',
        'datetime' => 'Y-m-d H:i:s',
        'timestamp' => 'Y-m-d H:i:s',
    ];

    public static function sanitizeDatetime($value, $format = 'Y-m-d', $strict = false)
    {
        if (isset(static::$dateFormats[$format])) {
            $format = static::$dateFormats[$format];
        }
        if ($value && ($time = strtotime($value)) !== false) {
            return date($format, $time);
        }
        return $strict ? date($format) : $value;
    }

    /**
     *
     * @param array $attributes
     * @param bool|array $rules
     * @return DynamicModel
     * @throws InvalidConfigException
     */
    public static function dynamicModel(array $attributes, $rules = true)
    {
        $model = new DynamicModel($attributes);
        if ($rules === true) {
            $attrs = [];
            foreach ($attributes as $key => $value) {
                $attrs[] = is_int($key) ? $value : $key;
            }
            $model->addRule($attrs, 'safe');
        } elseif (is_array($rules) && count($rules)) {
            $validators = $model->getValidators();
            foreach ($rules as $rule) {
                if ($rule instanceof Validator) {
                    $validators->append($rule);
                } elseif (is_array($rule) && isset($rule[0], $rule[1])) { // attributes, validator type
                    $validator = Validator::createValidator($rule[1], $model, (array) $rule[0], array_slice($rule, 2));
                    $validators->append($validator);
                } else {
                    throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
                }
            }
        }
        return $model;
    }

    /**
     *
     * @param ActiveQuery $query
     * @param string $expand
     */
    public static function expandWith($query, $expand)
    {
        if ($expand) {
            $expand = preg_split('/\s*,\s*/', $expand, -1, PREG_SPLIT_NO_EMPTY);
            $class = $query->modelClass;
            /* @var $model ActiveRecord */
            $model = $class::instance();
            $fields = [];
            foreach ($model->extraFields() as $key => $value) {
                if (is_int($key)) {
                    $fields[$value] = $value;
                } else {
                    $fields[$key] = $value;
                }
            }

            foreach ($expand as $field) {
                if (isset($fields[$field])) {
                    $query->with($fields[$field]);
                }
            }
        }
    }

    /**
     *
     * @param int $length
     * @return string
     */
    public static function generateUuid($length = 16)
    {
        $str = base_convert(uniqid(), 16, 36);
        while (strlen($str) < $length) {
            $str .= base_convert(mt_rand(100000, 999999), 10, 36);
        }
        return substr($str, 0, $length);
    }

    /**
     *
     * @param array $attributes
     * @return array
     */
    public static function buildSortAttributes($attributes = [])
    {
        $result = [];
        foreach ($attributes as $name => $attribute) {
            $result[is_int($name) ? $attribute : $name] = [
                'asc' => [$attribute => SORT_ASC],
                'desc' => [$attribute => SORT_DESC],
            ];
        }
        return $result;
    }

    public static function normalizePhone($phone)
    {
        $phone = trim((string) $phone);
        if (preg_match('/^\+([1-9][\- \d]+)$/', $phone, $matches)) {
            return str_replace(['-', ' '], '', $matches[1]);
        } elseif (preg_match('/^(62|0)([\- \d]+)$/', $phone, $matches)) {
            return '62' . str_replace(['-', ' '], '', $matches[2]);
        } elseif (preg_match('/^[1-9][\- \d]+$/', $phone, $matches)) {
            return str_replace(['-', ' '], '', $matches[0]);
        }
        return false;
    }

    /**
     * @param string $region_id
     * @return string[]
     */
    public static function splitRegion($region_id)
    {
        $ids = explode('.', $region_id);
        for ($i = 1; $i < count($ids); $i++) {
            $ids[$i] = $ids[$i - 1] . '.' . $ids[$i];
        }
        return $ids;
    }


    /**
     * @param string $table
     * @param string|int $id
     * @return string|null
     */
    public static function getItemDescription($table, $id)
    {
        $row = (new Query)
            ->from($table)
            ->where(['id' => $id])
            ->one();
        if ($row) {
            return $row['name'] ?? ($row['description'] ?? null);
        }
    }
}
