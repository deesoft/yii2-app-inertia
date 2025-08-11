<?php
$config = [
    'name' => 'Dee App Inertia',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
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
        // Looking to send emails in production? Check out our Email API/SMTP product!
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => env('MAILER_HOST', 'sandbox.smtp.mailtrap.io') ,
                'username' => env('MAILER_USERNAME'),
                'password' =>env('MAILER_PASSWORD'),
                'port' => env('MAILER_PORT',2525),
                'encryption' => env('MAILER_ENCRYPTION','tls'),
            ],
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
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
        'queue' => [
            'class' => \yii\queue\sync\Queue::class,
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
                'class' => 'dee\inertia\gii\Generator',
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
