<?php

use dee\inertia\ViteAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var View $this */
/** @var string $content */
$siteName = 'Dee App';
$siteDescription = 'Dee Inertia Apps.';
$themeColor = '#009D78';
$baseUrl = Url::base(true);

$this->registerCssFile('https://cdn.jsdelivr.net/npm/@mdi/font@6.9.96/css/materialdesignicons.min.css');

ViteAsset::register($this);
$user = Yii::$app->user->identity;
$this->registerJsVar('user', $user ? $user->toArray() : ['id' => null]);
$this->registerJsVar('menus', Yii::$app->assignedMenu);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => "$baseUrl/icon/icon.jpeg"]);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Dee APP</title>
        <?= Html::csrfMetaTags() ?>
        <?php $this->head(); ?>
    </head>
    <body>
        <?php $this->beginBody(); ?>
        <?= $content ?> 
        <?php $this->endBody(); ?>
    </body>
</html>
<?php $this->endPage(); ?>