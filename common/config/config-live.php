<?php
Yii::setAlias('@api-url', env('LIVE_API_URL'));
Yii::setAlias('@img-url', env('LIVE_MEDIA_URL'));
Yii::setAlias('@site-url', env('LIVE_SITE_URL'));

$config = [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => env('LIVE_DB_DSN'),
            'username' => env('LIVE_DB_USERNAME'),
            'password' => env('LIVE_DB_PASSWORD'),
            //'tablePrefix' => env('LIVE_DB_TABLE_PREFIX'),
            'charset' => env('LIVE_DB_CHARSET', 'utf8'),
            //'enableSchemaCache' => YII_ENV_PROD,
        ],
        'reCaptcha' => [
            'name' => 'reCaptcha',
            'class' => 'himiklab\yii2\recaptcha\ReCaptcha',
            'siteKey' => '',
            'secret' => '',
        ],
    ],
    'params' => [
        'google' => [
            'apiKey' => env('LIVE_GOOGLE_API'),
            'apiKey-server' => env('LIVE_GOOGLE_SERVER'),
        ],
    ],
];

return $config;