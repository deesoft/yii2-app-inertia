<?php

namespace app\models;

use app\classes\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\helpers\Inflector;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property string|null $phone
 * @property string|null $fullname
 * @property bool $active
 * @property string|null $avatar
 * @property string $initial 
 * @property int $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property OpenAuth[] $openAuths
 */
class User extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'required'],
            [['username', 'email', 'avatar', 'fullname'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 64],
            ['username', 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'avatar_id' => 'Avatar ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[OpenAuths]].
     *
     * @return ActiveQuery
     */
    public function getOpenAuths()
    {
        return $this->hasMany(OpenAuth::class, ['user_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getInitial()
    {
        $words = Inflector::camelize($this->fullname ? $this->fullname : $this->username);
        $words = explode(' ', Inflector::camel2words($words));
        $result = '';
        foreach ($words as $word) {
            $result .= substr($word, 0, 1);
        }
        return substr($result, 0, 2);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class
        ];
    }

    public function fields()
    {
        return [
            'id',
            'username',
            'email',
            'phone',
            'fullname',
            'avatar',
            'initial',
        ];
    }

    public function extraFields()
    {
        return [
        ];
    }
}
