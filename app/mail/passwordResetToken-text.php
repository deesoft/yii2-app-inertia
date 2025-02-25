<?php

use app\models\User;
use yii\web\View;

/* @var $this View */
/* @var $user User */

?>
Hello <?= $user->username ?>,

Follow the link below to reset your password:

<?= $resetLink ?>
