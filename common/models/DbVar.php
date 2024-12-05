<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "db_var".
 *
 * @property integer  $id
 * @property integer  $created
 * @property integer  $updated
 * @property string   $name
 * @property resource $data
 */
class DbVar extends \common\components\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName () {
        return 'db_var';
    }

    /**
     * @inheritdoc
     */
    public function rules () {
        return [
            [['created', 'updated', 'name', 'data'], 'required'],
            [['created', 'updated'], 'integer'],
            [['data'], 'string'],
            [['name'], 'string', 'max' => 45],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels () {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'data' => 'Data',
            'updated' => 'Updated',
            'created' => 'Created',
        ];
    }

    public function listSerialAttrs () {
        return array(
            'data',
        );
    }

    public function listCronStatus () {
        return [
            'active' => 'Processing',
            'inactive' => 'Idle',
            'silence' => 'Silenced',
            'disable' => 'Disabled',
        ];
    }

    public function getSearchAttrs () {
        return array(
            'name',
        );
    }

    public function getSearchOrder () {
        return [
            'name ASC',
        ];
    }

    public static function add ($name, $data) {
        $model = DbVar::find()->where(['name' => $name])->one();
        if (empty($model)) {
            $model = new DbVar();
            $model->name = $name;
        }
        $model->data = $data;

        return ($model->save() ? $model : false);
    }

    public static function clear ($name) {
        $model = DbVar::find()->where(['name' => $name])->one();
        if (empty($model)) {
            $model = new DbVar();
            $model->name = $name;
        }
        $model->data = [];

        return ($model->save() ? $model : false);
    }

    public static function findByName ($name) {
        return DbVar::find()->where(['name' => $name])->one();
    }

    public static function merge ($name, $data) {
        $model = DbVar::find()->where(['name' => $name])->one();
        if (empty($model)) {
            $model = new DbVar();
            $model->name = $name;
        }
        $temp = $model->data;
        ArrayHelper::merge($temp, $data);
        $model->data = $temp;

        return ($model->save() ? $model : false);
    }
}
