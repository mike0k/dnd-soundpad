<?php

namespace common\components;

use common\models\DbClient;
use common\models\DbVar;
use Yii;
use yii\base\Model;


class FormModel extends Model {

    public function getClassName () {
        return (new \ReflectionClass($this))->getShortName();
    }

    public function calcTax($val){
        $val = !empty($val) ? $val : 0;
        return round($val * Yii::$app->params['vat'], 2, PHP_ROUND_HALF_UP);
    }

    public function convertTime ($attrs, $convertTo = 'int', $strFormat = 'datetime') {
        if (!empty($attrs)) {
            if (!is_array($attrs)) {
                $attrs = array($attrs);
            }
            foreach ($attrs as $attr) {
                if (!empty($this->$attr)) {
                    if ($convertTo == 'int' && !is_numeric($this->$attr)) {
                        $this->$attr = strtotime(str_replace('/', '-', $this->$attr));
                        //check if strtotime() formatted correctly
                        if (empty($this->$attr) || !is_numeric($this->$attr) || $this->$attr < strtotime('1 Jan 2000')) {
                            $this->$attr = null;
                        }
                    } elseif ($convertTo == 'str' && is_numeric($this->$attr)) {
                        if ($strFormat == 'date') {
                            $this->$attr = Yii::$app->formatter->asDate($this->$attr);
                        } else {
                            $this->$attr = Yii::$app->formatter->asDatetime($this->$attr);
                        }
                    }
                }
            }
        }
    }

    public function getListLabel ($listName, $key) {
        $label = '';
        $list = $this->$listName();
        if (!empty($list)) {
            foreach ($list as $itemKey => $itemVal) {
                if ($key == $itemKey) {
                    $label = $itemVal;
                }
            }
        }

        return $label;
    }

    public function listBoolean () {
        return [
            0 => 'No',
            1 => 'Yes',
        ];
    }

    public function isTimestamp ($timestamp) {
        if(is_string($timestamp)){
            return ((string)(int)$timestamp === $timestamp)
                && ($timestamp <= PHP_INT_MAX)
                && ($timestamp >= ~PHP_INT_MAX);
        }else{
            return ($timestamp <= PHP_INT_MAX)
                && ($timestamp >= ~PHP_INT_MAX);
        }

    }

}