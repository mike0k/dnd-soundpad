<?php


namespace api\controllers;

use api\models\DbUser;
use common\models\DbUserSocket;
use common\models\DbUserToken;
use Yii;
use yii\helpers\ArrayHelper;

class SocketController extends \api\components\Controller {

    public $modelClass = 'common\models\DbSocket';

    public function behaviors () {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['except'] = ArrayHelper::merge($behaviors['authenticator']['except'], [
            'session',
            'validate-token',
        ]);

        return $behaviors;
    }

    public function actionLink () {
        $params = $this->getParams(['socketId']);
        $model = DbUserSocket::find()->where(['socketId' => $params['socketId']])->one();
        $valid = false;

        if (!empty($model)) {
            $model->userId = Yii::$app->user->id;
            $valid = $model->save();
            $logs = DbUserSocket::find()
                ->where(['userId' => $model->userId])
                ->andWhere(['<>', 'id', $model->id])
                ->all();
            if(!empty($logs)){
                foreach ($logs as $log){
                    $log->delete();
                }
            }
        }

        return [
            'valid' => $valid,
            'errors' => !empty($model) ? $model->errors : null,
        ];
    }

    public function actionSession () {
        $params = $this->getParams(['socketId', 'status', 'token']);
        $model = DbUserSocket::find()->where(['socketId' => $params['socketId']])->one();

        if (!empty($model)) {
            /*if ($params['status'] == 'link') {
                $model->userId = Yii::$app->user->id;
                if ($params['status'] == 'link') {
                    var_dump(Yii::$app->user->isGuest);
                    exit;
                }
                $model->save();
            }*/

            if ($params['status'] == 'disconnect') {
                if (!empty($model->userId)) {
                    DbUserSocket::deleteAll(['userId' => $model->userId]);
                } else {
                    $model->delete();
                }

            }
        }else{
            if ($params['status'] == 'connect') {
                $model = new DbUserSocket();
                $model->socketId = $params['socketId'];
                $model->save();
            }
        }

        return [
            'data' => $params,
            'errors' => !empty($model) ? $model->errors : null,
        ];
    }

    public function actionValidateToken () {
        $verified = $valid = false;
        $post = Yii::$app->request->post();
        if (!empty($post['token'])) {
            $userId = DbUserToken::checkAccess($post['token'], false);
            if (!empty($userId)) {
                $valid = true;

                $exists = DbUser::find()->where([
                    'id' => $userId,
                    'status' => 'active',
                ])->count();
                if(!empty($exists)){
                    $verified = true;
                }else{

                }
            }
        }

        return [
            'valid' => $valid,
            'verified' => $verified,
        ];
    }

}
