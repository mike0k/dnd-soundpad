<?php

namespace api\components;

use common\models\DbUserLogin;
use yii;
use yii\filters\AccessControl;
use yii\helpers\Json;

/**
 * Base controller
 */
//class Controller extends \common\components\Controller {
class Controller extends yii\rest\ActiveController {

    public $enableCsrfValidation = false;

    public function actions () {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    public static function allowedDomains () {
        if(YII_ENV == 'dev') {
            return [
                //'*', //allow all
                'http://localhost:19006',
                'http://localhost:8001', //React dev server - /site
                'http://192.168.1.222:5554', //React dev server - /app
            ];
        }else{
            return [
                '*',
                //'https://www.odetro.co.uk',
                //'https://account.odetro.co.uk'
            ];
        }
    }

    public function behaviors () {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        $behaviors['contentNegotiator'] = [
            'class' => yii\filters\ContentNegotiator::className(),
            'formats' => [
                'application/json' => yii\web\Response::FORMAT_JSON,
            ],
        ];

        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => static::allowedDomains(),
                //'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 3600,
                //'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
            ],
        ];

        $behaviors['authenticator'] = [
            'class' => yii\filters\auth\HttpBearerAuth::className(),
            /*'class' => yii\filters\auth\CompositeAuth::className(),
            'authMethods' => [
                yii\filters\auth\HttpBasicAuth::className(),
                yii\filters\auth\HttpBearerAuth::className(),
                yii\filters\auth\QueryParamAuth::className(),
            ],*/
            'except' => [
                'options',
            ],
        ];

        return $behaviors;
    }

    public function beforeAction ($action) {
        $return = parent::beforeAction($action);

        //$this->checkAccess();
        return $return;
    }

    public function afterAction ($action, $result) {
        return parent::afterAction($action, $result);
    }

    public function getParams ($attrs = [], $type = 'post') {
        switch (strtolower($type)) {
            case 'get':
                $raw = Yii::$app->request->get();
                break;
            case 'post':
            default:
                $raw = Yii::$app->request->post();
                break;
        }

        $return = [];
        foreach ($attrs as $attr) {
            if (isset($raw[$attr])) {
                $return[$attr] = $raw[$attr];
            } else {
                $return[$attr] = null;
            }
        }

        return $return;
    }

    /*public function checkAccess ($action, $model = null, $params = []) {
        if(!empty($params['token'])){
            var_dump($params);exit;
        }
    }*/

    protected function verbs () {
        return [
            'index' => ['GET', 'POST', 'HEAD', 'OPTIONS'],
            'list' => ['POST', 'OPTIONS'],
            'view' => ['GET', 'POST', 'HEAD', 'OPTIONS'],
            'create' => ['POST', 'OPTIONS'],
            'update' => ['PUT', 'PATCH', 'OPTIONS'],
            'delete' => ['DELETE', 'OPTIONS'],
        ];
    }

    function socket ($params) {
        $return = false;
        $url = Yii::getAlias('@socket-url') . '/' . str_replace('.', '/', $params['action']);
        $postData = yii\helpers\Json::encode($params);
        $ch = curl_init();

        try {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($postData))
            );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, count($params));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

            $output = curl_exec($ch);
        } catch (\Exception $exception) {
            $output = null;
            $return = $exception;
        }

        curl_close($ch);

        if (!empty($output)) {
            $return = json_decode($output);
            if(json_last_error() == JSON_ERROR_NONE){
                $return = $output;
            }
        }

        return $return;
    }

    public function updateUser(){
        $socketId = Yii::$app->user->identity->socket->socketId;
        return Yii::$app->controller->socket([
            'action' => 'game.update',
            'socketId' => $socketId,
        ]);
    }
}