<?php
$config = [
    'id' => 'basic-console',
    'bootstrap' => ['queue'],
    'controllerNamespace' => 'app\commands',
    'components' => [
        
    ],
    
];

if (YII_IS_LOCAL) {
     
}

return $config;
