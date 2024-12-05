<?php

namespace api\models;

use common\components\FormModel;
use common\models\DbTrackCount;
use Yii;

/**
 * Class FormUserEdit
 * @package api\models
 *
 * @property integer           $countId
 * @property integer           $date
 * @property string            $mode
 * @property string            $type
 * @property integer           $size
 * @property integer           $time
 *
 * @property-read DbTrackCount $count
 */
class FormTrackCount extends FormModel {

    private $_count;

    public $mode;

    public $countId;
    public $type;
    public $size = 1;
    public $date;
    public $time;

    public function rules () {
        return [
            [['type'], 'required', 'on' => ['add']],
            [['countId'], 'required', 'on' => ['delete']],

            [['size', 'countId', 'date', 'time'], 'integer'],
            [['mode', 'type'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels () {
        return [];
    }

    private function listMode () {
        return [
            'add',
            'delete',
            'list',

            'date',
            'time',
        ];
    }

    public function listPostAttrs () {
        return [
            'mode',

            'countId',
            'type',
            'size',
            'date',
            'time',
        ];
    }

    public function add () {
        $valid = false;
        $model = new DbTrackCount();

        if (!empty($this->mode)) {
            $this->setScenario($this->mode);
        }

        if ($this->validate()) {
            $model->attributes = [
                'type' => $this->type,
                'size' => $this->size,
            ];
            $model->userId = Yii::$app->user->id;

            if ($model->save()) {
                $valid = true;
            } else {
                $this->addErrors($model->errors);
            }
        }

        return ($valid ? $model : false);
    }

    public function delete () {
        $valid = false;
        $models = DbTrackCount::find()->where(['id' => $this->countId])->all();
        if (!empty($models)) {
            $valid = true;
            foreach ($models as $model) {
                if (!$model->delete()) {
                    $valid = false;
                }
            }
        }

        return $valid;
    }

    public function save () {
        $valid = false;

        if (!empty($this->mode)) {
            $this->setScenario($this->mode);
        }

        if ($this->validate()) {
            if (!empty($this->count)) {
                $model = $this->count;
                switch ($this->mode) {
                    case 'date':
                        $date = strtotime('today', $this->date);
                        $time = date('H:i', $model->created);
                        $model->created = strtotime($time, $date);
                        break;

                    case 'time':
                        $date = strtotime('today', $model->created);
                        $time = date('H:i', $this->time);
                        $model->created = strtotime($time, $date);
                        break;

                    default:
                        $attr = $this->mode;
                        if (isset($model->$attr) && isset($this->$attr)) {
                            $model->$attr = $this->$attr;
                        }
                        break;
                }

                if ($model->save()) {
                    $valid = true;
                } else {
                    $this->addErrors($model->errors);
                }
            } else {
                $this->addError('countId', 'Item not found');
            }
        }

        return ($valid ? $model : false);
    }

    public function baseData () {
        $return['valid'] = true;
        $data['counters'] = array_values(DbTrackCount::instance()->listCounter());
        $data['history'] = $this->statsCountList();
        $data['chart'] = $this->statsCountChart();
        $return['data'] = $data;


        return $return;
    }

    public function getCount () {
        if (empty($this->_count)) {
            $model = DbTrackCount::findOne(['id' => $this->countId]);
            if (!empty($model)) {
                $this->_count = $model;
            }
        }

        return $this->_count;
    }

    private function statsCountList () {
        $list = [];
        $counters = DbTrackCount::instance()->listCounter();
        $models = DbTrackCount::find()->filterWhere([
            'type' => $this->type,
            'size' => $this->size,
        ])
            ->orderBy('created DESC')
            ->limit(100)
            ->all();
        if (!empty($models)) {
            foreach ($models as $model) {
                if (!empty($counters[$model->type])) {
                    $thisYear = (date('Y', $model->created) === date('Y'));
                    $list[] = [
                        'id' => $model->id,
                        'typeId' => $model->type,
                        'sizeId' => $model->size,
                        'type' => $counters[$model->type]['id'],
                        'size' => array_search($model->size, $counters[$model->type]['size']),
                        'name' => $counters[$model->type]['name'],
                        'icon' => $counters[$model->type]['icon'],
                        'date' => Yii::$app->formatter->asDate($model->created, 'eee d MMM' . ($thisYear ? '' : ' yy')),
                        'time' => Yii::$app->formatter->asTime($model->created, 'h:mm a'),
                        'unix' => $model->created,
                    ];
                }
            }
        }

        return $list;
    }

    private function statsCountChart () {
        $return = [
            'keys' => [],
            'data' => [],
        ];

        $time = strtotime('-14 days');
        $end = time();

        $models = DbTrackCount::find()
            ->where(['userId' => Yii::$app->user->id])
            ->andWhere(['>=', 'created', $time])
            ->andWhere(['<', 'created', $end])
            ->groupBy('type')
            ->select('type')
            ->indexBy('type')
            ->all();
        $types = !empty($models) ? array_keys($models) : [];
        $return['keys'] = $types;

        while ($time < $end) {
            $data = [
                'date' => Yii::$app->formatter->asDate($time, 'eee d MMM'),
            ];

            foreach ($types as $type) {
                $count = DbTrackCount::find()
                    ->where([
                        'userId' => Yii::$app->user->id,
                        'type' => $type,
                    ])
                    ->andWhere(['>=', 'created', $time])
                    ->andWhere(['<', 'created', strtotime('+1 day', $time)])
                    ->sum('size');
                $count = empty($count) ? 0 : $count;
                $data[$type] = $count;
            }

            $return['data'][] = $data;
            $time = strtotime('+1 day', $time);
        }

        return $return;
    }


    public function scenarios () {
        $scenarios = parent::scenarios();
        foreach ($this->listMode() as $key => $val) {
            $scenarios[$val] = [];
        }

        return $scenarios;
    }

}