<?php
namespace yii\web {

use app\models\Auth;
use yii\base\Application as BaseApplication;
use yii\base\Component;
use yii\rbac\DbManager;

    /**
     * 
     * @property DbManager $authManager
     */
    class Application extends BaseApplication
    {

        public function handleRequest($request)
        {

        }
    }

    /**
     * @property Auth $identity
     * @property string $fullname
     * @property string $name
     */
    class User extends Component
    {
        
    }

}