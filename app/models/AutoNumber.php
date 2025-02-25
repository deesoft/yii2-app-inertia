<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "autonumber".
 *
 * @property string $id
 * @property int|null $counter
 * @property int|null $updated_at
 */
class AutoNumber extends ActiveRecord
{
    public static $formats;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'autonumber';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['counter', 'updated_at'], 'default', 'value' => null],
            [['counter', 'updated_at'], 'integer'],
            [['id'], 'string', 'max' => 32],
            [['id'], 'unique'],
        ];
    }

    protected static function nextValue($format, $time = null, $alnum = false)
    {
        if (empty($time)) {
            $time = time();
        } elseif (!is_numeric($time)) {
            $time = strtotime($time);
        }
        // apply variable
        $format = preg_replace_callback('/<([\w\.]+)>/', function ($matchs) {
            return ArrayHelper::getValue(Yii::$app, $matchs[1], '');
        }, $format);

        $format = preg_replace_callback('/\{([^\}]+)\}/', function ($matchs) use ($time) {
            $romans = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $tr = ['\X' => '<[1]>', 'X' => '<[2]>',];
            $str = strtr($matchs[1], $tr);
            $str = date($str, $time);
            $m = date('m', $time);
            $tr = ['<[1]>' => 'X', '<[2]>' => $romans[$m - 1]];
            return strtr($str, $tr);
        }, $format);
        $key = strlen($format) <= 32 ? $format : md5($format);

        $command = Yii::$app->db->createCommand();
        $command->setSql('SELECT [[counter]] FROM {{auto_number}} WHERE [[id]]=:key');
        $counter = $command->bindValue(':key', $key)->queryScalar() + 1;
        $number = $alnum ? strtoupper(base_convert($counter, 10, 36)) : (string) $counter;

        $places = [];
        $total = 0;
        $result = preg_replace_callback('/\?+/', function ($matchs) use (&$places, &$total) {
            $n = strlen($matchs[0]);
            $i = count($places);
            $places[] = $n;
            $total += $n;
            return "<[~{$i}~]>";
        }, $format);

        if ($total > 1) {
            $number = str_pad($number, $total, '0', STR_PAD_LEFT);
            $tr = [];
            for ($i = count($places) - 1; $i >= 0; $i--) {
                if ($i == 0) {
                    $tr["<[~{$i}~]>"] = $number;
                } else {
                    $tr["<[~{$i}~]>"] = substr($number, -$places[$i]);
                    $number = substr($number, 0, -$places[$i]);
                }
            }
            $result = strtr($result, $tr);
        } else {
            $result = str_replace('?', $number, $format);
        }
        return [$result, $key, $counter];
    }

    public static function generate($format, $time = null, $alnum = false)
    {
        list($number, $key, $counter) = static::nextValue($format, $time, $alnum);
        Yii::$app->db->createCommand()
            ->upsert('auto_number', ['id' => $key, 'counter' => $counter, 'update_at' => time()])
            ->execute();
        return $number;
    }

    public static function next($format, $time = null, $alnum = false)
    {
        list($number,,) = static::nextValue($format, $time, $alnum);
        return $number;
    }

    public static function nextAt($table, $format = null, $column = 'number', $alnum = true, $time = null, $filter = [])
    {
        $esc = [
            '%' => '\%',
            '_' => '\_',
            '\\' => '\\\\',
        ];
        if (empty($time)) {
            $time = time();
        } elseif (!is_numeric($time)) {
            $time = strtotime($time);
        }

        if (class_exists($table) && is_subclass_of($table, ActiveRecord::class)) {
            $table = $table::tableName();
        }
        if (empty($format)) {
            $format = self::$formats[$table] ?? '?';
        }
        $format = preg_replace_callback('/\{([^\}]+)\}/', function ($matchs) use ($time) {
            $romans = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $tr = ['\X' => '<[1]>', 'X' => '<[2]>',];
            $str = strtr($matchs[1], $tr);
            $str = date($str, $time);
            $m = date('m', $time);
            $tr = ['<[1]>' => 'X', '<[2]>' => $romans[$m - 1]];
            return strtr($str, $tr);
        }, $format);
        $like = str_replace('?', '_', strtr($format, $esc));
        $query = (new Query())
            ->select(['v' => "MAX([[$column]])"])
            ->from($table)
            ->where(['ILIKE', $column, $like, false]);
        if ($filter) {
            $query->andFilterWhere($filter);
        }
        $current = $query->scalar();
        if (!$current) {
            $current = str_replace('?', '0', $format);
        }
        $number = '';
        $padLen = 0;
        for ($i = 0; $i < strlen($format); $i++) {
            if ($format[$i] === '?') {
                $number .= $current[$i];
                $padLen++;
            }
        }
        if ($alnum) {
            $number = base_convert($number, 36, 10);
        }
        $number = ((int) $number) + 1;
        if ($alnum) {
            $number = strtoupper(base_convert($number, 10, 36));
        }
        $number = str_pad($number, $padLen, '0', STR_PAD_LEFT);

        $result = '';
        $n = strlen($number) - 1;
        for ($i = strlen($format) - 1; $i >= 0; $i--) {
            $result = ($format[$i] === '?' ? $number[$n] : $format[$i]) . $result;
            $n--;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'counter' => 'Counter',
            'updated_at' => 'Updated At',
        ];
    }
}

AutoNumber::$formats = [];
