<?php
require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../common/env.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

$config = [];
$configs = [
    '/../../common/config/config.php',
    '/../config/config.php',
    '/../../common/config/config-' . env('YII_CONFIG') . '.php',
    '/../config/config-' . env('YII_CONFIG') . '.php',
];
foreach($configs as $file){
    if(file_exists(__DIR__ . $file)){
        $config = yii\helpers\ArrayHelper::merge($config, require(__DIR__ . $file));
    }
}

$application = new yii\web\Application($config);
$application->run();
