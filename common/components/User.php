<?php

namespace common\components;

use common\models\DbUser;
use yii;

/**
 * User
 */
class User extends yii\web\User {

    private $authKey;
    public $accessToken;

    protected $_dbUser;

    public function getAttr ($attr) {
        $return = '';
        $this->getDbUser();
        if (!empty($this->_dbUser)) {
            $return = $this->_dbUser->$attr;
        }

        return $return;
    }

    public function getEmail () {
        return $this->getAttr('email');
    }

    public function getId () {
        $return = null;
        if(!empty($this->identity)){
            $return = $this->identity->id;
        }

        return $return;
    }

    public function getName () {
        return $this->getAttr('name');
    }

    public function getFullName () {
        return $this->getAttr('fullName');
    }

    public function getDbUser () {
        if (empty($this->_dbUser) && !$this->isGuest) {
            $user = DbUser::findOne($this->id);
            if (!empty($user)) {
                $this->_dbUser = $user;
            }
        }

        return $this->_dbUser;
    }


    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity ($id) {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken ($token, $type = null) {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey () {
        return $this->authKey;
    }

    public function logout ($destroySession = true) {
        return parent::logout($destroySession);
    }

    /**
     * @param string $authKey
     * @return bool if auth key is valid for current user
     */
    public function validateAuthKey ($authKey) {
        return $this->getAuthKey() === $authKey;
    }

}