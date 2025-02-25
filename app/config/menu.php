<?php
return [
    ['title' => 'Home', 'icon' => 'mdi-view-dashboard', 'route' => '/site/index'],    
    ['title' => 'Admin', 'icon' => '', 'children' => [
        ['title' => 'User', 'icon' => 'mdi-account', 'route' => '/admin/user/'],
        ['title' => 'Role', 'icon' => '', 'route' => '/admin/role/'],
    ]],
    ['title' => 'About', 'icon' => '', 'route' => '/site/about'],
];
