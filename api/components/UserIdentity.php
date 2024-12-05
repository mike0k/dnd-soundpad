<?php

namespace api\components;

use api\models\DbUser;
use common\models\DbUserToken;
use yii\base\NotSupportedException;

class UserIdentity extends DbUser implements \yii\web\IdentityInterface {

    /**
     * @inheritdoc
     */
    public static function findIdentity ($id) {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken ($token, $type = null) {
        $user = null;
        $userId = DbUserToken::checkAccess($token);
        if (!empty($userId)) {
            $user = static::findOne($userId);
        }

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function getId () {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey () {
        return $this->salt;
    }


    /**
     * @inheritdoc
     */
    public function validateAuthKey ($authKey) {
        return $this->salt === $authKey;
    }

}
