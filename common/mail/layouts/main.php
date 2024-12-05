<?php
use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $content string main view render result */

$imgUrl = Yii::getAlias('@img-url') . '/';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width"/>
    <title><?= Html::encode(Yii::$app->params['company']['name']) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<table class="body">
    <tr>
        <td class="float-center" align="center" valign="top">
            <center data-parsed>
                <!-- HEADER-->
                <table class="wrapper header" align="center">
                    <tr>
                        <td class="wrapper-inner">
                            <?php echo Yii::$app->mailer->render('common/_spacer', ['size' => 32]); ?>
                            <table align="center" class="container headerTop">
                                <tbody>
                                <tr>
                                    <td>
                                        <table class="row collapse">
                                            <tbody>
                                            <tr>
                                                <th class="small-12 large-12 columns first last">
                                                    <table>
                                                        <tr>
                                                            <th>
                                                                <center data-parsed>
                                                                <img src="<?= $imgUrl ?>logo/logo-li-sm.png" alt="<?= Html::encode(Yii::$app->params['company']['name']) ?>" />
                                                                </center>
                                                            </th>
                                                        </tr>
                                                    </table>
                                                </th>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <?php echo Yii::$app->mailer->render('common/_spacer', ['size' => 16]); ?>
                            <table align="center" class="container headerInner">
                                <tbody>
                                <tr>
                                    <td>
                                        <table class="row collapse">
                                            <tbody>
                                            <tr>
                                                <th class="small-12 large-12 columns first last headerInnerCol">
                                                    <table>
                                                        <tr>
                                                            <th>
                                                                <img src="<?= $imgUrl ?>mail/header-test-top.png" />
                                                            </th>
                                                        </tr>
                                                    </table>
                                                </th>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- CONTENT-->
                <table align="center" class="container float-center">
                    <tbody>
                    <tr>
                        <td>
                            <img src="<?= $imgUrl ?>mail/header-test-bottom.png" />
                            <?php echo Yii::$app->mailer->render('common/_spacer'); ?>
                            <?= $content ?>
                            <?php echo Yii::$app->mailer->render('common/_spacer'); ?>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <!-- FOOTER-->
                <table class="wrapper footer" align="center">
                    <tr>
                        <td class="wrapper-inner">
                            <?php echo Yii::$app->mailer->render('common/_spacer', ['size' => 32]); ?>
                            <table align="center" class="container">
                                <tbody>
                                <tr>
                                    <td>
                                        <table class="row collapse">
                                            <tbody>
                                            <tr>
                                                <th class="small-12 large-12 columns first last">
                                                    <table>
                                                        <tr>
                                                            <th>
                                                                <center data-parsed="">
                                                                    <table align="center" class="menu float-center">
                                                                        <tr>
                                                                            <td>
                                                                                <table>
                                                                                    <tr>
                                                                                        <th class="menu-item float-center">
                                                                                            <a href="#"><img src="<?= $imgUrl ?>mail/facebook.png" alt="Facebook" /></a>
                                                                                        </th>
                                                                                        <th class="menu-item float-center">
                                                                                            <a href="#"><img src="<?= $imgUrl ?>mail/twitter.png" alt="Twitter" /></a>
                                                                                        </th>
                                                                                        <th class="menu-item float-center">
                                                                                            <a href="#"><img src="<?= $imgUrl ?>mail/linkedin.png" alt="LinkedIn" /></a>
                                                                                        </th>
                                                                                    </tr>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </center>
                                                                <?php echo Yii::$app->mailer->render('common/_spacer'); ?>
                                                                <p class="text-center">
                                                                    <small>
                                                                        <a href="tel:<?php echo Yii::$app->params['company']['phone']; ?>"><?php echo Yii::$app->formatter->asPhone(Yii::$app->params['company']['phone']); ?></a>
                                                                        |
                                                                        <a href="<?php echo Yii::getAlias('@site-url'); ?>"><?php echo Yii::getAlias('@site-url'); ?></a>
                                                                    </small>
                                                                </p>
                                                                <?php echo Yii::$app->mailer->render('common/_spacer'); ?>
                                                                <p class="text-center">
                                                                    <small>
                                                                        &copy; <?php echo Yii::$app->params['company']['name']; ?>. All Rights Reserved.
                                                                    </small>
                                                                </p>
                                                            </th>
                                                        </tr>
                                                    </table>
                                                </th>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </center>
        </td>
    </tr>
</table>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
