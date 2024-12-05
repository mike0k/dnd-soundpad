<?php

namespace api\components;

use api\models\DbUser;
use common\models\DbUserToken;
use yii;

/**
 * User
 */
class User extends \common\components\User {

    public function getDbUser () {
        if (empty($this->_dbUser) && !$this->isGuest) {
            $user = DbUser::find()->where(['id' => $this->id])->one();
            if (!empty($user)) {
                $this->_dbUser = $user;
            }
        }

        return $this->_dbUser;
    }

}