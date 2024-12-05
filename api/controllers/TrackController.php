<?php


namespace api\controllers;

use api\models\FormTrackCount;
use Yii;

class TrackController extends \api\components\Controller {

    public $modelClass = 'common\models\DbUser';

    public function actionAdd () {
        $return = [
            'errors' => new \ArrayObject(),
            'valid' => false,
        ];
        $model = new FormTrackCount();
        $params = $this->getParams($model->listPostAttrs());
        $model->attributes = $params;
        $model->mode = 'add';

        if ($model->add()) {
            $return['valid'] = true;
        } else {
            $return['errors'] = $model->errors;
        }

        return $return;
    }

    public function actionCount(){
        $model = new FormTrackCount();
        $params = $this->getParams($model->listPostAttrs());
        $model->attributes = $params;

        return $model->baseData();
    }

    public function actionEdit(){
        $return = [
            'errors' => new \ArrayObject(),
            'valid' => false,
        ];
        $model = new FormTrackCount();
        $params = $this->getParams($model->listPostAttrs());
        $model->attributes = $params;

        if ($model->save()) {
            $return['valid'] = true;
        } else {
            $return['errors'] = $model->errors;
        }

        return $return;
    }

    public function actionRemove(){
        $return = [
            'valid' => false,
        ];
        $model = new FormTrackCount();
        $params = $this->getParams($model->listPostAttrs());
        $model->attributes = $params;
        $model->mode = 'delete';

        $return['valid'] = $model->delete();

        return $return;
    }


}
