<?php

namespace api\models;

use common\components\FormModel;
use Yii;

/**
 * Class FormUserEdit
 * @package api\models
 *
 * @property string $newPass
 * @property string $confirmPass
 *
 * @property DbUser $user
 *
 */
class FormUserPassword extends FormModel {

    public $newPass;
    public $confirmPass;

    private $_user;

    public function rules () {
        return [
            [['newPass', 'confirmPass'], 'required'],
            [['newPass', 'confirmPass'], 'string', 'max' => 255],

            //re-enable to allow password checking
            //[['newPass'], 'customValidation'],
        ];
    }

    public function attributeLabels () {
        return [
            'newPass' => 'New Password',
            'confirmPass' => 'Confirm password',
        ];
    }

    public function getUser () {
        if (empty($this->_user)) {
            $this->setUser(Yii::$app->user->id);
        }

        return $this->_user;
    }

    public function setUser($userId){
        $userId = empty($userId) ? Yii::$app->user->id : $userId;
        $user = DbUser::findOne($userId);
        if (!empty($user)) {
            $this->_user = $user;
        }
    }

    public function save ($user = null) {
        $valid = false;

        if(!empty($user)){
            $this->_user = $user;
        }

        if ($this->validate()) {
            if(!empty($this->user)){
                $this->user->passwordNew = $this->newPass;
                $this->user->passwordNewConfirm = $this->confirmPass;
                $this->user->setScenario('password');
                if($this->user->save()){
                    $valid = true;
                }else{
                    $this->errors = $this->user->errors;
                }
            }else{
                $this->addError('email', 'Account not found');
            }
        }

        return $valid;
    }

    public function customValidation () {
        if (!empty($this->newPass) || !empty($this->confirmPass)) {
            $valid = true;

            if ($valid && $this->newPass != $this->confirmPass) {
                $valid = false;
                $this->addError('newPass', 'Passwords do not match');
                $this->addError('confirmPass', 'Passwords do not match');
            }

            if ($valid && (strlen($this->newPass) < 6)) {
                $valid = false;
                $this->addError('newPass', 'Password must be at least 6 characters long');
            }

            if($valid){
                $containsLower = preg_match('/[a-z]/', $this->newPass);
                $containsUpper = preg_match('/[A-Z]/', $this->newPass);
                $containsDigit = preg_match('/\d/', $this->newPass);
                //$containsSpecial = preg_match('/[^a-zA-Z\d]/', $this->passwordNew);


                if(!$containsLower || !$containsUpper){
                    $valid = false;
                    //$this->addError('newPass', 'Password must contain at least 1 uppercase and 1 lowercase character');
                }

                if (!$containsDigit) {
                //if (!$containsDigit && !$containsSpecial) {
                    $valid = false;
                    //$this->addError('newPass', 'Password must contain at least 1 number or 1 symbol');
                }

                if(!$valid){
                    $this->addError('newPass', 'Password must contain at least 1 uppercase character, 1 lowercase character and 1 number');
                }
            }
        }
    }

}