<?php

namespace common\components;

use yii;

/**
 * View
 */
class View extends yii\web\View {

    public function beforeRender ($viewFile, $params) {
        $return = parent::beforeRender($viewFile, $params);
        $this->registerMetaData();

        return $return;
    }

    protected function registerMetaData ($data = null) {
        $title = $action = $controller = '';

        if (!empty($data)) {
            foreach ($data as $key => $val) {
                if ($key == 'title') {
                    $title = $val;
                } else {
                    $this->registerMetaTag([
                        'name' => $key,
                        'content' => $val
                    ]);
                }
            }
        }

        if (!empty($title)) {
            if (Yii::$app->controller->id != 'site') {
                $controller = Yii::$app->controller->id;
                $sep = ' ';
            }

            if (Yii::$app->controller->action->id != 'index') {
                $action = Yii::$app->controller->action->id;
            }
            $title = Yii::$app->formatter->asCamelCase($action) . ' ' . Yii::$app->formatter->asCamelCase($controller);;
        }

        $title .= (!empty($title) ? ' | ' : '') . Yii::$app->name;
        $this->title = substr($title, 0, 69);
    }


    public function registerJs ($js, $position = self::POS_END, $key = null) {
        parent::registerJs($js, $position, $key);
    }

}