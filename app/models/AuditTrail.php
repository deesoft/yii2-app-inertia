<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "audit_trail".
 *
 * @property int $id
 * @property string $table
 * @property int|null $row_id
 * @property string|null $row_id_str
 * @property string $action
 * @property string|null $time
 * @property int|null $user_id
 * @property string|null $note
 * @property string|null $before
 * @property string|null $after
 */
class AuditTrail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'audit_trail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['table', 'action'], 'required'],
            [['row_id', 'user_id'], 'default', 'value' => null],
            [['row_id', 'user_id'], 'integer'],
            [['time', 'before', 'after'], 'safe'],
            [['table', 'row_id_str', 'action'], 'string', 'max' => 64],
            [['note'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'table' => 'Table',
            'row_id' => 'Row ID',
            'row_id_str' => 'Row Id Str',
            'action' => 'Action',
            'time' => 'Time',
            'user_id' => 'User ID',
            'note' => 'Note',
            'before' => 'Before',
            'after' => 'After',
        ];
    }
}
