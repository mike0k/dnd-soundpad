<?php

namespace api\models;

use common\components\FormModel;
use Yii;

/**
 * Class FormUserEdit
 * @package api\models
 *
 * @property string  $email
 * @property string  $name
 * @property string  $mode
 * @property string  $phone
 *
 * @property DbUser  $user
 *
 */
class FormUserEdit extends FormModel {

    public $mode;

    public $email;
    public $name;
    public $phone;

    private $_user;

    public function rules () {
        return [
            [['name'], 'required', 'on' => ['name']],
            [['email'], 'required', 'on' => ['email']],

            [['email'], 'email'],
            [['night'], 'integer'],
            [['name','email',], 'string', 'max' => 255],
            [['mode'], 'string', 'max' => 20],
            [['phone'], 'string', 'max' => 15],
        ];
    }

    public function attributeLabels () {
        return [
            'email' => 'Email',
            'name' => 'Name',
            'phone' => 'Phone',
        ];
    }

    private function listMode () {
        return [
            'details',
        ];
    }

    public function listPostAttrs () {
        return [
            'mode',

            'email',
            'name',
            'phone',
        ];
    }

    public function getUser () {
        if (empty($this->_user)) {
            $this->_user = DbUser::findOne(Yii::$app->user->id);
        }

        return $this->_user;
    }

    public function save () {
        $valid = false;

        if (!empty($this->mode)) {
            $this->setScenario($this->mode);
        }

        if ($this->validate()) {
            if (!empty($this->user)) {
                $model = $this->user;
                switch ($this->mode) {
                    case 'details':
                        $model->name = $this->name;
                        $model->email = $this->email;
                        $model->phone = $this->phone;
                        break;

                    default:
                        $attr = $this->mode;
                        if (isset($model->$attr) && isset($this->$attr)) {
                            $model->$attr = $this->$attr;
                        }
                        break;
                }

                if ($model->save()) {
                    $valid = true;
                } else {
                    $this->addErrors($model->errors);
                }
            } else {
                $this->addError('email', 'Account not found');
            }
        }

        return ($valid ? $this->user : false);
    }


    public function scenarios () {
        $scenarios = parent::scenarios();
        foreach ($this->listMode() as $key => $val) {
            $scenarios[$val] = [];
        }

        return $scenarios;
    }

}