<?php


namespace api\controllers;

use api\models\DbUser;
use api\models\FormLogin;
use api\models\FormUserEdit;
use api\models\FormUserPassword;
use common\models\DbTrackCount;
use common\models\DbUserSocket;
use common\models\DbUserToken;
use Yii;
use yii\helpers\ArrayHelper;

class UserController extends \api\components\Controller {

    public $modelClass = 'common\models\DbUser';

    public function behaviors () {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['except'] = ArrayHelper::merge($behaviors['authenticator']['except'], [
            'login',
        ]);

        return $behaviors;
    }

    public function actionEdit () {
        $return = [
            'errors' => new \ArrayObject(),
            'valid' => false,
        ];
        $model = new FormUserEdit();
        $params = $this->getParams($model->listPostAttrs());
        $model->attributes = $params;

        if ($model->save()) {
            $return['valid'] = true;
        } else {
            $return['errors'] = $model->errors;
        }

        return $return;
    }

    public function actionLogin () {
        $return = [
            'valid' => false,
            'token' => '',
            'id' => '',
            'errors' => [],
        ];

        $model = new FormLogin();
        $model->attributes = Yii::$app->request->post();
        if ($model->login()) {
            $return['token'] = $model->getToken();
            $return['id'] = $model->user->id;
            if (!empty($return['token'])) {
                $return['valid'] = true;
            }
        } else {
            $return['errors'] = $model->errors;

        }

        return $return;
    }

    public function actionLogout(){
        $params = $this->getParams([
            'token',
        ]);
        if(!empty($params['token'])){
            $token = DbUserToken::find()
                ->where([
                    'userId' => Yii::$app->user->id,
                    'token' => $params['token'],
                ])->one();
            if(!empty($token)){
                $token->delete();
            }
        }
        //Yii::$app->user->logout();

        return ['valid' => true];
    }

    public function actionPassword () {
        $return = [
            'errors' => new \ArrayObject(),
            'valid' => false,
        ];
        $model = new FormUserPassword();
        $params = $this->getParams([
            'newPass',
            'confirmPass',
        ]);
        $model->attributes = $params;

        if ($model->save()) {
            $return['valid'] = true;
        } else {
            $return['errors'] = $model->errors;
        }

        return $return;
    }

    public function actionView () {
        $return = [];
        $model = DbUser::findOne(Yii::$app->user->id);
        if (!empty($model)) {
            $return = $model->getViewData(false);
        }

        return $return;
    }
}
