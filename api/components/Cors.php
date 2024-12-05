<?php

namespace api\components;

use Yii;

class Cors extends \yii\filters\Cors {

    public function beforeAction ($action) {
        parent::beforeAction($action);

        if (Yii::$app->getRequest()->getMethod() === 'OPTIONS') {
            Yii::$app->getResponse()->getHeaders()->set('Allow', 'POST GET PUT');
            Yii::$app->end();
        }

        return true;
    }

    public function prepareHeaders($requestHeaders) {
        $responseHeaders = parent::prepareHeaders($requestHeaders);
        if (isset($this->cors['Access-Control-Allow-Headers'])) {
            $responseHeaders['Access-Control-Allow-Headers'] = implode(', ', $this->cors['Access-Control-Allow-Headers']);
        }

        return $responseHeaders;
    }

}