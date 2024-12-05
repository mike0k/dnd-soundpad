<?php


namespace api\controllers;

use common\components\GoogleDrive;
use Yii;
use yii\helpers\ArrayHelper;

class FinanceController extends \api\components\Controller {

    public $modelClass = 'common\models\DbSocket';

    public function behaviors () {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['except'] = ArrayHelper::merge($behaviors['authenticator']['except'], [
            'index',
        ]);

        return $behaviors;
    }

    public function actionIndex(){
        $drive = new GoogleDrive();
        $drive->listDocs();

        var_dump('test');
        exit;
    }

}
