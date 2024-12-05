<?php

use \yii\web\Request;


Yii::setAlias('@web', dirname(dirname(__DIR__)) . '/web');
$baseUrl = str_replace('api/web', 'api', (new Request)->getBaseUrl());
$hostUrl = (new Request)->getHostInfo();

$callback = isset($_REQUEST['callback']) ? $_REQUEST['callback'] : false;
$format = $callback ? 'jsonp' : 'json';

$config = [
    'id' => 'animite-property-api',
    'name' => 'Animite Property API',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'cors' => [
            'class' =>'api\components\Cors'
        ],
        'request' => [
            'cookieValidationKey' => env('API_COOKIE_VALIDATION_KEY'),
            'baseUrl' => $baseUrl,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'format' => $format, // json or jsonp
            'on beforeSend' => function ($event) {
                $callback = isset($_REQUEST['callback']) ? $_REQUEST['callback'] : false;
                $response = $event->sender;
                if ($response->data !== null) {
                    /*$response->data = [
                        'success' => $response->isSuccessful,
                        'data' => $response->data,
                    ];*/
                    /*if ($callback) {
                        $response->data += ['callback' => $callback];
                    }*/
                    //$response->statusCode = 200;
                }
            },
        ],
        'urlManager' => [
            'baseUrl' => $baseUrl,
            'hostInfo' => $hostUrl,
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'media/view/<ref>' => 'media/view',

                'login' => 'user/login',
                'logout' => 'user/logout',
                'validate-token' => 'socket/validate-token',
                'session-link' => 'socket/link',
                'session' => 'socket/session',

                ['class' => 'yii\rest\UrlRule', 'controller' => 'media'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'property'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],

                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/' => '<controller>/<action>',
            ],
        ],
        'user' => [
            'class' => 'api\components\User',
            'identityClass' => 'api\components\UserIdentity',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => '',
        ],
    ],
];

return $config;
