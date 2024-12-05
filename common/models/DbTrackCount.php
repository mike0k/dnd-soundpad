<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Db_track_count".
 *
 * @property integer $id
 * @property integer $created
 * @property integer $userId
 * @property string  $type
 * @property integer $size
 */
class DbTrackCount extends \common\components\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName () {
        return 'db_track_count';
    }

    /**
     * @inheritdoc
     */
    public function rules () {
        return [
            [['userId', 'type', 'size'], 'required'],
            [['created', 'userId', 'size'], 'integer'],
            [['type'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels () {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'type' => 'Type',
            'size' => 'Size',
            'updated' => 'Updated',
            'created' => 'Created',
        ];
    }

    public function listCounter () {
        return [
            'bed' => [
                'id' => 'bed',
                'name' => 'Bedtime',
                'icon' => 'bed',
                'size' => [
                    'Early' => 1,
                    'Normal' => 2,
                    'Late' => 3,
                    'Very Late' => 4,
                ],
            ],
            'weed' => [
                'id' => 'weed',
                'name' => 'Weed',
                'icon' => 'cannabis',
                'size' => [
                    'Light' => 1,
                    'Normal' => 2,
                    'Heavy' => 3,
                    'Maximus' => 4,
                ],
            ],
            'beer' => [
                'id' => 'beer',
                'name' => 'Alohol',
                'icon' => 'glass-mug-variant',
                'size' => [
                    'Just a couple' => 1,
                    'Half-Cut' => 2,
                    'Drunk' => 3,
                    'Wasted' => 4,
                ],
            ],
            'sex' => [
                'id' => 'sex',
                'name' => 'Lust',
                'icon' => 'emoticon-cool-outline',
                'size' => [
                    'Quickie' => 1,
                    'Session' => 2,
                    'Mega Session' => 3,
                ],
            ],
            'love' => [
                'id' => 'love',
                'name' => 'Love',
                'icon' => 'heart',
                'size' => [
                    'Tickles' => 1,
                    'Session' => 2,
                    'Mega Session' => 3,
                ],
            ],
            'run' => [
                'id' => 'run',
                'name' => 'Exercise',
                'icon' => 'run',
                'size' => [
                    'Light Session' => 1,
                    'Session' => 2,
                    'Mega Session' => 3,
                ],
            ],
        ];
    }

    public function listType () {
        $list = [];
        foreach ($this->listCounter() as $data){
            $list[$data['id']] = $data['name'];
        }

        return $list;
    }

    public static function add ($attrs) {
        $model = new DbTrackCount();
        $model->attributes = $attrs;
        $model->userId = Yii::$app->user->id;

        return ($model->save() ? $model : false);
    }
}
