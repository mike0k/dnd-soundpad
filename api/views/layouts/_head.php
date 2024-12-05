<?php
use yii\helpers\Html;
?>

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="canonical" href="<?= Yii::$app->request->hostInfo.Yii::$app->request->url; ?>"/>
    <meta name="author" content="Animite Media"/>
    <?= Html::csrfMetaTags() ?>

    <title><?= Html::encode($this->title) ?></title>

    <?php

    $this->head()
    ?>


    <?php $this->head() ?>
</head>