<?php

use yii\console\Application as ConsoleApplication;
use yii\web\Application;

/** @var Application|ConsoleApplication $app */

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
    ]
]);

// allowed routes
app\helpers\Access::$allowedAuthtRoutes = [
    '/*'
];
app\helpers\Access::$allowedGuestRoutes = [
    //'/*'
];
