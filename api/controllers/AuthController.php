<?php

namespace api\controllers;

use api\components\Controller;
use common\models\DbVar;use Yii;
use yii\helpers\ArrayHelper;

class AuthController extends Controller{

    public $modelClass = 'common\models\DbSocket';

    public function behaviors () {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['except'] = ArrayHelper::merge($behaviors['authenticator']['except'], [
            'google',
        ]);

        return $behaviors;
    }

    public function actionGoogle () {
        if (!empty($_REQUEST['code'])) {
            DbVar::add('google-auth-token', ['code' => $_REQUEST['code']]);
        }
    }

}