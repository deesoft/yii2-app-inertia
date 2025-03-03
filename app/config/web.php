<?php
$config = [
    'id' => 'basic',
    'name' => 'main',
    'controllerNamespace' => 'app\controllers',
    'components' => [
        'request' => [
            'cookieValidationKey' => env('COOKIE_VALIDATION_KEY'),
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            //'enableStrictParsing' => true,
            'rules' => require(__DIR__ . '/routes.php'),
        ],
        'session' => [
            'class' => 'yii\web\DbSession'
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\Auth',
            'enableAutoLogin' => true,
            'enableSession' => true,
            'loginUrl' => ['/auth/login'],
        ],
    ],
    'as access' => [
        'class' => app\classes\AccessControl::class,
        'allowed' => [
            '*', // remark bagian ini setelah user role tersedia.
            'site/about'
        ],
        'menus' => __DIR__ . '/menu.php',
        'except' => [
            'file/*',
            'gii/*',
            'auth/*',
        ],
    ],
    'params' => [
        'inertia' => [
            'encript_history' => true,
            'viewFile' => '@app/views/app.php',
        ]
    ]
];

if (YII_IS_LOCAL) {
    // configuration adjustments for 'dev' environment
}

return $config;
