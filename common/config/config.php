<?php

Yii::setAlias('@api', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('@api-web', dirname(dirname(__DIR__)) . '/api/web');

Yii::setAlias('@site', dirname(dirname(__DIR__)) . '/site');
Yii::setAlias('@site-web', dirname(dirname(__DIR__)) . '/site/build');

Yii::setAlias('@common', dirname(dirname(__DIR__)) . '/common');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@mail', dirname(dirname(__DIR__)) . '/common/mail');
Yii::setAlias('@media', dirname(dirname(__DIR__)) . '/media');
Yii::setAlias('@socket', dirname(dirname(__DIR__)) . '/socket');


$config = [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'bootstrap' => ['log'],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'formatter' => [
            'class' => 'common\components\Formatter',
            'currencyCode' => 'GBP',
            'dateFormat' => 'php:j M Y',
            'datetimeFormat' => 'php:j M Y H:i',
            'timeFormat' => 'php:H:i',
        ],
        'log' => [
            'traceLevel' => 3,
            'targets' => [
                [
                    'class' => 'common\components\Log',
                    'levels' => ['error','warning'],
                    'logVars' => [],
                ],
            ],
        ],
        'mailer' => [
            'class' => 'common\components\Mailer',
            'viewPath' => '@mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'session' => [
            'class' => 'yii\web\DbSession',
            'sessionTable' => 'yii_session',
        ],
        'view' => [
            'class' => 'common\components\View',
        ],
    ],
    'params' => [
        'email' => [
            'debug' => 'michael@animitemedia.com',
            'from' => 'no-reply@animitemedia.com',
        ],
        'company' => [
            'name' => 'Animite Property',
            'address' => '3 Kennet Cottages, Kennet, Allo, FK10 4DN',
            'email' => 'info@animitemedia.com',
            'phone' => '07545304228',
            'lat' => 56.100110,
            'lng' => -3.728200,
            'reg' => '',
        ],

        'vat' => 0.2,

        'salt' => env('SALT'),
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
];

return $config;