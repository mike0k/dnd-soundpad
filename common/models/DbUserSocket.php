<?php

namespace common\models;

use common\components\ActiveRecord;
use Yii;

/**
 * This is the model class for table "db_user_socket".
 *
 * @property int         $id
 * @property int         $created
 * @property int         $updated
 * @property string      $socketId
 * @property int         $userId
 *
 * @property-read DbUser $user
 */
class DbUserSocket extends ActiveRecord {
    /**
     * {@inheritdoc}
     */
    public static function tableName () {
        return 'db_user_socket';
    }

    /**
     * {@inheritdoc}
     */
    public function rules () {
        return [
            [['socketId'], 'required'],
            [['created', 'updated', 'userId'], 'integer'],
            [['socketId'], 'string', 'max' => 255],
            [['socketId'], 'unique']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels () {
        return [
            'id' => 'ID',
            'created' => 'Created',
            'updated' => 'Updated',
            'socketId' => 'Websocket ID',
            'userId' => 'User ID',
        ];
    }

    public function getUser () {
        return $this->hasOne(DbUser::className(), ['id' => 'userId']);
    }

}
