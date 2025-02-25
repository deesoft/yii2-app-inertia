<?php

namespace app\models\form;

use app\models\AuthItem;
use Yii;
use yii\base\Model;

/**
 * Description of Role
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Role extends Model
{
    public $name;
    public $description;

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'match', 'pattern' => '/^[\w-]+$/'],
            [['description'], 'string'],
            [['name'], 'unique', 'targetClass' => AuthItem::class],
        ];
    }

    public function save()
    {
        $rbac = Yii::$app->authManager;
        $rbac->add(new \yii\rbac\Role([
            'name' => $this->name,
            'description' => $this->description,
        ]));
    }
}
