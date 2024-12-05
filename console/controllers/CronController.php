<?php

namespace console\controllers;

use common\components\Maintenance;
use yii\console\Controller;


/**
 * Class CronController
 * @package console\controllers
 *
 * @property string $cmd
 */
class CronController extends Controller {

    public $cmd;

    public function options ($actionID) {
        return ['cmd'];
    }

    public function optionAliases () {
        return ['c' => 'cmd'];
    }

    public function actionIndex () {
        //example cPanel command: 	/usr/local/bin/php -q /home/ACCOUNT/public_html/app/console/index cron/index -c=min
        $model = new Maintenance();

        if ($this->cmd == 'test') {
            var_dump('start');

            var_dump('end');
            exit;
        }

        $valid = $model->cronCollision($this->cmd, 'active');
        if ($valid) {
            switch ($this->cmd) {
                case 'min':
                    //active cron
                    //$model->sendMail();
                    break;

                case 'hour':
                    //inactive cron
                    break;

                case 'day':
                    //active cron
                    //$model->cleanLogs();
                    break;

                case 'week':
                    //inactive cron
                    break;

                case 'month':
                    //active cronzz
                    break;
            }

            $model->cronCollision($this->cmd, 'inactive');
        }
    }


}
