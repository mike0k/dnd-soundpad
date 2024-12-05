<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="page-content">
    <div class="content-header">
        <div class="container">
            <h3>Error: <?= Html::encode($this->title) ?></h3>
        </div>
    </div>

    <div class="container">
        <?php
        if(YII_DEBUG || !Yii::$app->user->isGuest) {
            echo '<p>Error Message: <strong>' . nl2br(Html::encode($message)) . '</strong></p>';
            //echo '<p><strong>'.$error['file'].' (Line: '.$error['line'].')</strong></p>';
            echo '<hr />';
            echo '<p>' . nl2br(Html::encode($exception)) . '</p>';
        }
        ?>
    </div>

</div>
