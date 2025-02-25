<?php


$config = [
    'id' => 'basic',
    'name' => 'main',
    'controllerNamespace' => 'app\controllers',
    'components' => [
        'request' => [
            'cookieValidationKey' => env('COOKIE_VALIDATION_KEY'),
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'multipart/form-data' => 'yii\web\MultipartFormDataParser'
            ],
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
            'loginUrl' => ['site/login'],
        ],
    ],
    'as access' => [
        'class' => app\classes\AccessControl::class,
        'except' => [
            'file/*',
            'gii/*',
            //'*',
        ],
    ],
    'params' => [
        'inertia' =>[
            'encript_history' => true,
            'viewFile' => '@app/views/app.php',
        ]
    ]
];

if (YII_IS_LOCAL) {
    // configuration adjustments for 'dev' environment
      
}

return $config;
