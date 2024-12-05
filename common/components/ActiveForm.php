<?php


namespace common\components;

use yii\helpers\ArrayHelper;

class ActiveForm extends \yii\bootstrap4\ActiveForm {

    public $enableAjaxValidation = false;
    public $enableClientValidation = false;
    public $fieldClass = 'common\components\ActiveField';
    public $errorSummaryCssClass = 'alert alert-danger';

}