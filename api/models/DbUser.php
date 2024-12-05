<?php

namespace api\models;

use Yii;


class DbUser extends \common\models\DbUser {

    public function getViewData($public = true) {
        if(!$public) {
            return $this->getPrivate();
        }else {
            return $this->getPublic();
        }
    }

    private function getPrivate(){
        $return = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
        ];

        return $return;
    }

    private function getPublic(){
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
