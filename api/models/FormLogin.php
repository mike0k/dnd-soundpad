<?php

namespace api\models;

use common\components\Mailer;
use common\models\DbUserToken;
use Yii;
use yii\base\Model;

/**
 * Class FormLogin
 * @package api\models
 *
 * @property string           $email
 * @property string           $password
 *
 * @property-read string      $hash
 * @property-read DbUser      $user
 * @property-read DbUserToken $token
 */
class FormLogin extends Model {
    public $email;
    public $password;

    private $_hash;
    private $_token;
    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules () {
        $rules = [
            [['email', 'password'], 'required', 'on' => 'login'],
            ['password', 'validatePassword', 'on' => 'login'],

            [['email'], 'required', 'on' => 'reset'],

            [['email', 'password'], 'string', 'max' => 255],
            [['email'], 'email'],
        ];

        return $rules;
    }

    public function attributeLabels () {
        return [
            'email' => 'Email Address',
            'password' => 'Password',
            'reCaptcha' => '',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array  $params the additional name-value pairs given in the rule
     */
    public function validatePassword ($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

//            $user->passwordNew = $this->password;
//            $user->updatePassword();
//            $user->save();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect email or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided email and password.
     * @return bool whether the user is logged in successfully
     */
    public function login () {
        $valid = false;
        $this->setScenario('login');
        if ($this->validate()) {
            $valid = $this->loginUser();
        }

        return $valid;
    }

    public function loginUser ($userId = null) {
        $valid = false;
        if (!empty($userId)) {
            $this->_user = DbUser::findOne($userId);
        }
        if (!empty($this->user)) {
            $this->_token = DbUserToken::add($this->user->id);
            if (!empty($this->_token)) {
                $valid = true;
            }
        }

        return $valid;
    }

    public function getHash () {
        return $this->_hash;
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    public function getUser () {
        if (empty($this->_user)) {
            $this->_user = \api\components\UserIdentity::find()->where(['email' => $this->email])->one();
        }

        return $this->_user;
    }

    public function getToken () {
        return $this->_token;
    }

    public function sendReset () {
        $valid = false;
        $this->setScenario('reset');
        if ($this->validate()) {
            if (!empty($this->user)) {
                $mailer = new Mailer();
                $mail = $mailer->add($this->user, 'password-reset');
                if (!empty($mail)) {
                    $valid = true;
                    $hash = $mailer->getHash();
                    if (!empty($hash) && !empty($hash->hash)) {
                        $this->_hash = $hash->hash;
                    }
                }
            } else {
                $this->addError('email', 'Account not found');
            }
        }

        return $valid;
    }

}
