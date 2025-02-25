<?php
require __DIR__ . '/../app/config/bootstrap.php';
$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../app/config/common.php',
    require __DIR__ . '/../app/config/web.php'
);

(new yii\web\Application($config))->run();
