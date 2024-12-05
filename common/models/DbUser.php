<?php

namespace common\models;

use common\components\ActiveQuery;
use Yii;

/**
 * This is the model class for table "db_user".
 *
 * @property integer             $id
 * @property integer             $created
 * @property integer             $updated
 * @property string              $status
 * @property string              $name
 * @property string              $email
 * @property string              $phone
 * @property string              $password
 * @property string              $salt
 *
 * @property-read DbUserSocket   $socket
 */
class DbUser extends \common\components\ActiveRecord {

    public $passwordNew;
    public $passwordNewConfirm;
    public $authKey;

    public function beforeSave ($insert) {
        $this->updatePassword();

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public static function tableName () {
        return 'db_user';
    }

    /**
     * @inheritdoc
     */
    public function rules () {
        return [
            [['status', 'password', 'name', 'email'], 'required'],
            [['passwordNew', 'passwordNewConfirm'], 'required', 'on' => 'password'],
            [['created', 'updated'], 'integer'],
            [['status'], 'string', 'max' => 45],
            [['email', 'password', 'salt', 'name'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 15],
            [['email'], 'unique'],
            [['passwordNew', 'passwordNewConfirm'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels () {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'password' => 'Password',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'salt' => 'Salt',
            'updated' => 'Updated',
            'created' => 'Created',

            'passwordNew' => 'New Password',
            'passwordNewConfirm' => 'Confirm Password',
        ];
    }

    public function getHash () {
        return base64_encode($this->salt . Yii::$app->params['salt']);
    }

    public function getSearchAttrs () {
        return array(
            'firstName',
            'lastName',
            'email',
        );
    }

    public function getSearchOrder () {
        return [
            'status ASC',
            'firstName ASC',
            'lastName ASC',
            'email ASC',
        ];
    }

    public function getSocket () {
        return $this->hasOne(DbUserSocket::className(), ['userId' => 'id']);
    }

    public function getTokens () {
        return $this->hasMany(DbUserToken::className(), ['userId' => 'id']);
    }

    public function listDeleteRelations () {
        return [
            'pref',
            'socket',
            'tokens',
            'userAssigns',
        ];
    }

    public function setDefaults () {
        if (empty($this->salt)) {
            $this->salt = Yii::$app->security->generateRandomString();
        }
        if (empty($this->password)) {
            $this->passwordNew = Yii::$app->security->generateRandomString(10);
            $this->updatePassword();
        }

        if (empty($this->status)) {
            $this->status = 'active';
        }

        parent::setDefaults();
    }

    public function updatePassword () {
        if (!empty($this->passwordNew)) {
            $password = base64_encode($this->passwordNew . $this->hash);
            $this->password = Yii::$app->security->generatePasswordHash($password);
        }
    }


    public function validatePassword ($password) {
        $password = base64_encode($password . $this->hash);
        return Yii::$app->security->validatePassword($password, $this->password);
    }
}
