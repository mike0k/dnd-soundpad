<?php


namespace common\components;

use yii\helpers\ArrayHelper;

class ActiveField extends \yii\bootstrap4\ActiveField {

    public $horizontalCheckboxTemplate = "{beginWrapper}\n<div class=\"checkbox\">\n{beginLabel}\n{input}\n<span class=\"label-text\">{labelTitle}</span>\n{endLabel}\n</div>\n{error}\n{endWrapper}\n{hint}";
    public $radioTemplate = '';

    /**
     * @inheritdoc
     */
    public function __construct ($config = []) {
        $config['horizontalCssClasses']['wrapper'] = 'col-sm-9';
        parent::__construct($config);
    }

    public function addon ($options = []) {
        $options = (!empty($options['addon']) ? $options['addon'] : $options);
        $options = ArrayHelper::merge([
            'btn' => false,
            'prepend' => [],
            'append' => [],
        ], $options);

        $btn = 'addon';
        if ($options['btn']) {
            $btn = 'btn';
        }

        $prepend = $this->addonContent($options['prepend']);
        $append = $this->addonContent($options['append']);

        if(!empty($prepend['content']) && !empty($append['content'])){
            $this->inputTemplate = '
<div class="input-group">
<div class="input-group-prepend input-group-'.$btn.'" ' . $prepend['tooltip'] . ' data-placement="right"><span class="input-group-text">' . $prepend['content'] . '</span></div>
{input}
<div class="input-group-append input-group-'.$btn.'" ' . $append['tooltip'] . ' data-placement="left"><span class="input-group-text">' . $append['content'] . '</span></div>
</div>';

        } else if (!empty($prepend['content'])){
            $this->inputTemplate = '
<div class="input-group">
<div class="input-group-prepend input-group-'.$btn.'" ' . $prepend['tooltip'] . ' data-placement="right"><span class="input-group-text">' . $prepend['content'] . '</span></div>
{input}
</div>';

        } else if (!empty($append['content'])){
            $this->inputTemplate = '
<div class="input-group">
{input}
<div class="input-group-append input-group-'.$btn.'" ' . $append['tooltip'] . ' data-placement="left"><span class="input-group-text">' . $append['content'] . '</span></div>
</div>';

        }

        return $this;
    }

    private function addonContent($options){
        $content = $tooltip = '';
        if(!empty($options['ref'])) {
            switch($options['ref']){
                case 'date':
                    $content = '<i class="fa fa-calendar"></i>';
                    $tooltip = 'Select a date (e.g 1 Jan '.date('Y').')';
                    break;

                case 'datetime':
                    $content = '<i class="fa fa-calendar"></i>';
                    $tooltip = 'Select a date (e.g 14:30 1 Jan '.date('Y').')';
                    break;

                case 'money':
                    $content = '<i class="fa fa-pound-sign"></i>';
                    $tooltip = 'Enter a monetary value';
                    break;

                case 'select':
                    $content = '<i class="fa fa-circle"></i>';
                    $tooltip = 'Select One';
                    break;

                case 'select-multiple':
                    $content = '<i class="fa fa-ellipsis-v"></i>';
                    $tooltip = 'Select Multiple';
                    break;

            }
        }
        if (!empty($options['content'])) {
            $content = $options['content'];
        }
        if (!empty($options['tooltip'])) {
            $content = $options['tooltip'];
        }

        if(!empty($tooltip)){
            $tooltip = ' data-toggle="tooltip" title="'.$tooltip.'"';
        }

        return [
            'content' => $content,
            'tooltip' => $tooltip,
        ];
    }

    public function input ($type, $options = []) {
        $this->addon($options);

        return parent::input($type, $options);
    }

    public function dropDownList ($items, $options = []) {
        $this->addon($options);

        return parent::dropDownList($items, $options);
    }

    public  function radioList ($items, $options = []) {

        if(!empty($options['btn'])) {
            $options['item'] = function ($index, $label, $name, $checked, $value) {

                $return = '<label class="radio-inline btn btn-default">';
                $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" '.($checked ? 'checked' : '').' />';
                $return .= '<span>' . ucwords($label) . '</span>';
                $return .= '</label>';

                return $return;
            };
        }

        return parent::radioList($items, $options);
    }

    public function staticControl ($options = []) {
        $this->addon($options);

        return parent::staticControl($options);
    }

    public function textarea ($options = []) {
        $this->addon($options);

        return parent::textarea($options);
    }

    public function textInput ($options = []) {
        $this->addon($options);

        return parent::textInput($options);
    }

}