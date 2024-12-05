<?php

namespace api\models;

use common\components\FormModel;
use common\components\Mailer;
use common\models\DbUserPref;
use Yii;

/**
 * Class FormUserAdd
 * @package api\models
 *
 * @property string  $email
 * @property string  $firstName
 * @property string  $lastName
 *
 * @property-read string      $hash
 * @property-read DbUser  $user
 *
 */
class FormUserAdd extends FormModel {

    public $email;
    public $firstName;
    public $lastName;

    private $_hash;
    private $_user;

    public function rules () {
        return [
            [['firstName', 'lastName', 'email'], 'required'],

            [['email'], 'email'],
            [['email'], 'string', 'max' => 255],
            [['firstName', 'lastName'], 'string', 'max' => 45],
        ];
    }

    public function attributeLabels () {
        return [
            'email' => 'Email',
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
        ];
    }

    public function listPostAttrs () {
        return [
            'email',
            'firstName',
            'lastName',
        ];
    }

    public function getHash () {
        return $this->_hash;
    }

    public function getUser () {
        return $this->_user;
    }

    public function save () {
        $valid = false;
        $user = new DbUser();

        if ($this->validate()) {
            $user->attributes = [
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'email' => $this->email,
            ];

            if($user->save()){
                $valid = true;

                $pref = new DbUserPref();
                $pref->userId = $user->id;
                if(!$pref->save()){
                    $valid = false;
                    $this->addErrors($pref->errors);
                }
            }else{
                $this->addErrors($user->errors);
            }

            if($valid) {
                $mailer = new Mailer();
                $mail = $mailer->add($user, 'verify-email');
                if (!empty($mail)) {
                    $valid = true;
                    $hash = $mailer->getHash();
                    if (!empty($hash) && !empty($hash->hash)) {
                        $this->_hash = $hash->hash;
                    }
                }
            }else if(!empty($user->id)){
                $user->delete();
            }
        }
        $this->_user = $user;

        return $valid;
    }

}