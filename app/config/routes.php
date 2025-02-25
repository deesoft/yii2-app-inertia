<?php
return[
    'OPTIONS <x:.*>' => 'site/options',
    '' => 'site/index',
    'login' => 'site/login',
    'GET,HEAD file/<id>/<width:\d+>x<height:\d+>' => 'file/view',
    'GET,HEAD file/<id>/w<width:\d+>' => 'file/view',
    'GET,HEAD file/<id>/h<height:\d+>' => 'file/view',
    'GET,HEAD file/<id>' => 'file/view',
    'DELETE file/<id:\w+>' => 'file/delete',
    'POST file' => 'file/upload',

];
