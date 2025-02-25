<?php

$config = [
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', function($app){
        if(file_exists(__DIR__ . '/init.php')){
            require __DIR__ . '/init.php';
        }
        if($app instanceof yii\console\Application && file_exists(__DIR__ . '/init_console.php')){
            require __DIR__ . '/init_console.php';
        }
        if($app instanceof yii\web\Application && file_exists(__DIR__ . '/init_web.php')){
            require __DIR__ . '/init_web.php';
        }
    }],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'runtimePath' => dirname(dirname(__DIR__)) . '/runtime',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@client' => dirname(dirname(__DIR__)) . '/client',
        '@app/web' => dirname(dirname(__DIR__)) . '/web',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\DbCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'cache' => 'cache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => env('DB_DSN'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8',
        ],
        'dbFile' => [
            'class' => 'yii\db\Connection',
            'dsn' => env('DB_FILE_DSN', env('DB_DSN')),
            'username' => env('DB_FILE_USERNAME', env('DB_USERNAME')),
            'password' => env('DB_FILE_PASSWORD', env('DB_PASSWORD')),
            'charset' => 'utf8',
        ],
        'mutex' => [
            'class' => \yii\mutex\PgsqlMutex::class,
        ],
    ],
    'params' => [],
];

if (YII_IS_LOCAL) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'inertia' => [
                'class' => 'dee\gii\generators\inertia\Generator',
                'modelNsSearch' => [
                    'app\models'
                ]
            ],
            'model' => ['class' => 'dee\gii\generators\model\Generator'],
        ]
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
