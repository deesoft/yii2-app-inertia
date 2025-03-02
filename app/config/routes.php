<?php
return[
    'OPTIONS <x:.*>' => 'site/options',
    '' => 'site/index',
    'login' => 'auth/login',
    'auth/reset-password/<token>' => 'auth/reset-password',
    'GET,HEAD file/<id>/<width:\d+>x<height:\d+>' => 'file/view',
    'GET,HEAD file/<id>/w<width:\d+>' => 'file/view',
    'GET,HEAD file/<id>/h<height:\d+>' => 'file/view',
    'GET,HEAD file/<id>' => 'file/view',
    'DELETE file/<id:\w+>' => 'file/delete',
    'POST file/<type:\w+>' => 'file/upload',
    'POST file' => 'file/upload',
];
