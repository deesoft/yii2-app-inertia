<?php

use dee\inertia\ViteAsset;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var string $content */
$this->registerCssFile('https://cdn.jsdelivr.net/npm/@mdi/font@6.9.96/css/materialdesignicons.min.css');

ViteAsset::register($this);
$user = Yii::$app->user->identity;
$this->registerJsVar('user', $user ? $user->toArray() : ['id' => null]);
$this->registerJsVar('menus', Yii::$app->assignedMenu);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Yii::getAlias('@web/icon/icon.jpeg')]);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?= Yii::$app->name ?></title>
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