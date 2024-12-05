<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\widgets\Breadcrumbs;
use dash\assets\PageAsset;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<?php echo $this->render('_head'); ?>
<body>

<?php $this->beginBody() ?>


<?php echo $content; ?>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
