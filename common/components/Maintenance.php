<?php

namespace common\components;

use common\models\DbMailHash;
use common\models\DbMailOutbox;
use common\models\DbMedia;
use common\models\DbProperty;
use common\models\DbPropertyFinance;
use common\models\DbVar;
use common\models\FormReport;
use common\models\MsProperty;
use yii\helpers\ArrayHelper;
use yii;

class Maintenance extends Component {

    public function cleanLogs () {
        $models = DbMailHash::find()
            ->where(['<', 'expire', time()])
            ->all();
        if (!empty($models)) {
            foreach ($models as $model) {
                $model->delete();
            }
        }
    }

    public function cronCollision ($cron, $status, $force = false) {
        $ban = '24 hours';
        $life = '5 min';

        $name = 'cron-' . $cron;
        $valid = true;
        $data = [
            'status' => 'inactive',
            'count' => 0,
            'startTime' => null,
            'endTime' => null,
        ];

        //get cron collision log
        $log = DbVar::findByName($name);

        //create new log if missing
        if (empty($log)) {
            $log = DbVar::add($name, $data);
        }

        //merge log data with default data
        if (!empty($log)) {
            $data = ArrayHelper::merge($data, $log->data);
        }

        if ($data['status'] == 'disable') {
            $valid = false;
        }

        //false if cron is silenced unless it hasn't ran for 1 day
        if ($data['status'] == 'silence' && $data['startTime'] >= (strtotime('-' . $ban))) {
            $valid = false;
        }

        //false if cron is currently running unless cron has been active for more than an hour
        if ($data['status'] == 'active' && $data['startTime'] >= (strtotime('-' . $life))) {
            $valid = false;
        }

        //silence cron if it has more than 5 continuous collisions
        if (!$force && $data['count'] >= 5 && $data['startTime'] <= (strtotime('-' . $life))) {
            $status = 'silence';
        }

        //process requested status
        if ($valid || $force || ($status == 'inactive' && $data['status'] == 'active')) {
            $data['status'] = $status;
            switch ($status) {
                case 'active':
                    $data['startTime'] = time();
                    $data['endTime'] = null;
                    $data['count'] = 0;
                    break;
                case 'inactive':
                    $data['endTime'] = time();
                    $data['count'] = 0;
                    break;
            }

            $log->data = $data;
            $valid = $log->save();
        } else {
            $data['count']++;
            $log->data = $data;
            $log->save();
        }

        return $valid;
    }

    public function sendMail () {
        $models = DbMailOutbox::find()
            ->where(['status' => 'wait'])
            ->andWhere(['<=', 'runTime', time()])
            ->limit(10)
            ->all();
        if (!empty($models)) {
            $mailer = Yii::$app->mailer;
            foreach ($models as $model) {
                $mailer->sendMail($model);
            }
        }
    }

}