<?php
defined('ROOT_APP_PATH') or define('ROOT_APP_PATH', dirname(dirname(__DIR__)));

require ROOT_APP_PATH . '/vendor/autoload.php';
require __DIR__ . '/env.php';

loadEnv(ROOT_APP_PATH);

defined('YII_DEBUG') or define('YII_DEBUG', env('DEBUG', false));
defined('YII_ENV') or define('YII_ENV', env('ENV', 'prod'));
defined('YII_IS_LOCAL') or define('YII_IS_LOCAL', env('IS_LOCAL', false));

require ROOT_APP_PATH . '/vendor/yiisoft/yii2/Yii.php';

// ************************* BATAS EDIT ****************************
Yii::$container->setDefinitions([
    yii\behaviors\TimestampBehavior::class => [
        'value' => function () {
            return date('Y-m-d H:i:s');
        }
    ],
    yii\data\Sort::class => [
        'enableMultiSort' => true,
    ],
    yii\data\Pagination::class => [
        'pageSizeLimit' => [1, 1000],
    ],
    yii\queue\sync\Queue::class => [
        'handle' => true,
    ]
]);
