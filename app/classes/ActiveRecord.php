<?php

namespace app\classes;

use app\models\AuditTrail;
use app\models\AutoNumber;
use Yii;
use yii\db\ActiveRecord as BaseActiveRecord;
use yii\web\Request;
use yii\web\User;

/**
 * Description of ActiveRecord
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class ActiveRecord extends BaseActiveRecord
{
    public $audit_trail_user;
    public $audit_trail_note;
    protected $audit_trail_exclude = false;

    /**
     * {@inheritDoc}
     */
    public static function deleteAll($condition = null, $params = [])
    {
        $columns = static::getTableSchema()->columns;
        if (isset($columns['is_deleted'])) {
            $value = ['is_deleted' => true];
            if (isset($columns['active'])) {
                $value['active'] = false;
            }
            if (isset($columns['deleted_at'])) {
                $value['deleted_at'] = date('Y-m-d H:i:s');
            }
            return static::updateAll($value, $condition, $params);
        }
        return parent::deleteAll($condition, $params);
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['created_at'], $fields['created_by'], $fields['updated_at'], $fields['updated_by']);
        return $fields;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->audit_trail_exclude) {
            return;
        }
        $before = $after = [];
        $new = $this->attributes;
        foreach ($changedAttributes as $key => $value) {
            if (!in_array($key, ['created_at', 'created_by', 'updated_at', 'updated_by'])) {
                $before[$key] = $value;
                $after[$key] = $new[$key];
            }
        }
        if ($insert) {
            $before = null;
        }
        if (empty($before) && empty($after)) {
            return;
        }
        $pkey = $this->primaryKey;
        $info = Yii::$container->invoke([$this, 'getAuditTrailBaseInfo']);
        $values = [
            'table' => static::tableName(),
            'action' => $insert ? 'insert' : 'update',
            'note' => $info['note'],
            'user_id' => $info['user_id'],
            'time' => date('Y-m-d H:i:s'),
            'before' => $before ? $before : null,
            'after' => $after ? $after : null,
        ];
        if (is_array($pkey)) {
            $values['row_id_str'] = implode(',', $pkey);
        } else {
            $values['row_id_str'] = (string) $pkey;
            if (is_int($pkey) || preg_match('/^\d+$/', $pkey)) {
                $values['row_id'] = (int) $pkey;
            }
        }
        Yii::$app->db->createCommand()->insert('audit_trail', $values)->execute();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        if ($this->audit_trail_exclude) {
            return;
        }
        $before = $this->attributes;
        unset($before['created_at'], $before['created_by'], $before['updated_at'], $before['updated_by']);
        $pkey = $this->primaryKey;
        $info = Yii::$container->invoke([$this, 'getAuditTrailBaseInfo']);
        $values = [
            'table' => static::tableName(),
            'action' => 'delete',
            'note' => $info['note'],
            'user_id' => $info['user_id'],
            'time' => date('Y-m-d H:i:s'),
            'before' => $before,
            'after' => null,
        ];
        if (is_array($pkey)) {
            $values['row_id_str'] = implode(',', $pkey);
        } else {
            $values['row_id_str'] = (string) $pkey;
            if (is_int($pkey) || preg_match('/^\d+$/', $pkey)) {
                $values['row_id'] = (int) $pkey;
            }
        }
        Yii::$app->db->createCommand()->insert('audit_trail', $values)->execute();
    }

    /**
     * @return AuditTrail[]
     */
    public function getHistories()
    {
        $pkey = $this->getPrimaryKey();
        $where = ['table' => static::tableName()];
        if (is_array($pkey)) {
            $where['row_id_str'] = implode(',', $pkey);
        } elseif (is_int($pkey) || preg_match('/^\d+$/', $pkey)) {
            $where['row_id'] = $pkey;
        } else {
            $where['row_id_str'] = $pkey;
        }
        return AuditTrail::find()
            ->where($where)
            ->all();
    }

    public function getAuditTrailBaseInfo(User $user = null, Request $request = null)
    {
        $user_id = $this->audit_trail_user ?: ($user ? $user->id : null);
        $note = $this->audit_trail_note ?: ($request ? $request->post('audit_trail_note', null) : null);
        return [
            'user_id' => $user_id,
            'note' => $note,
        ];
    }

    /**
     * @return string
     */
    public static function nextNumber($format = null, $column = 'number', $alnum = true, $time = null, $filter = [])
    {
        return AutoNumber::nextAt(static::tableName(), $format, $column, $alnum, $time, $filter);
    }
}
