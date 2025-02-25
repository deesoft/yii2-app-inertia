<?php
$config = [
    'id' => 'basic-console',
    'controllerNamespace' => 'app\commands',
    'components' => [
        'urlManager' => [
            'baseUrl' => env('BASE_URL', ''),
            'hostInfo' => env('HOST_INFO', 'http://localhost'),
            'scriptUrl' => env('SCRIPT_URL', '/index.php'),
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => require(__DIR__ . '/routes.php'),
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\Auth',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => ['site/login'],
        ],
    ],
    
];

if (YII_IS_LOCAL) {
     
}

return $config;
