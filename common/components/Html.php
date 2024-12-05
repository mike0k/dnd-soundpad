<?php

namespace common\components;

use Yii;
use yii\helpers\Url;

class Html extends \yii\helpers\Html {

    public static function img($src, $options = []) {
        if (strpos($src, '/media/') === false && strpos($src, 'http://') === false && strpos($src, 'https://') === false && strpos($src, '@') === false) {
            $src = Yii::getAlias('@img-url') . '/' . $src;
        }

        if (!empty($options['lazy'])) {
            $options['data-src'] = Url::to($src);
            $src = Yii::getAlias('@img-url').'/misc/lazy-load.png';
            unset($options['lazy']);
        }

        return parent::img($src, $options);
    }

}