<?php

use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $user User */

?>
<div class="password-reset">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

    <p>Follow the link below to reset your password:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>
