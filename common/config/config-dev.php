<?php

Yii::setAlias('@api-url', env('DEV_API_URL'));
Yii::setAlias('@img-url', env('DEV_MEDIA_URL').'img/');
Yii::setAlias('@socket-url', env('DEV_SOCKET_URL'));
Yii::setAlias('@site-url', env('DEV_SITE_URL'));

$config = [
    'bootstrap' => ['debug', 'gii'],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => env('DEV_DB_DSN'),
            'username' => env('DEV_DB_USERNAME'),
            'password' => env('DEV_DB_PASSWORD'),
            'tablePrefix' => env('DEV_DB_TABLE_PREFIX'),
            'charset' => env('DEV_DB_CHARSET', 'utf8'),
            'enableSchemaCache' => YII_ENV_PROD,
        ],
        'reCaptcha' => [
            'name' => 'reCaptcha',
            'class' => 'himiklab\yii2\recaptcha\ReCaptcha',
            'siteKey' => '',
            'secret' => '',
        ],
    ],
    'modules' => [
        'debug' => [
            'class' => 'yii\debug\Module',
        ],
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['127.0.0.1', '::1'],
            'generators' => [
                'crud' => [
                    'class' => 'yii\gii\generators\crud\Generator',
                    'templates' => [
                        'Extended' => '@app/gii/extended',
                    ]
                ]
            ],
        ],
    ],
    'params' => [
        'google' => [
            'apiKey' => env('DEV_GOOGLE_API'),
            'apiKey-server' => env('DEV_GOOGLE_SERVER'),
        ],
    ],
];

return $config;