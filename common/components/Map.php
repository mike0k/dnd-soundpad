<?php

namespace common\components;

use yii;

class Map extends Component {

    public static function geocode ($address) {
        $address = urlencode($address);
        $i = 0;
        while ($i < 10) {
            $url = 'https://maps.google.com/maps/api/geocode/json?address=' . $address;
            if (YII_ENV == 'prod') {
                $url = 'https://maps.google.com/maps/api/geocode/json?address=' . $address . '&key=' . Yii::$app->params['google']['apiKey-server'];
            }

            $data = yii\helpers\Json::decode(file_get_contents($url));
            if ($data['status'] == 'OK') {
                $lat = $data['results'][0]['geometry']['location']['lat'];
                $lng = $data['results'][0]['geometry']['location']['lng'];

                if (!empty($lat) && !empty($lng)) {
                    return [
                        'lat' => $lat,
                        'lng' => $lng,
                    ];
                }
            }
            usleep(250000);
            $i++;
        }

        return false;
    }

    public static function queryRadius ($lat, $lng, $radius) {
        return ['<', '( 3959 * ACOS( COS( RADIANS( ' . $lat . ' ) ) * COS( RADIANS( lat ) ) * COS( RADIANS( lng ) - RADIANS( ' . $lng . ' ) ) + SIN( RADIANS( ' . $lat . ' ) ) * SIN( RADIANS( lat ) ) ) )', $radius];
    }

    public static function queryDistance ($lat, $lng) {
        return '( 3959 * ACOS( COS( RADIANS( ' . $lat . ' ) ) * COS( RADIANS( lat ) ) * COS( RADIANS( lng ) - RADIANS( ' . $lng . ' ) ) + SIN( RADIANS( ' . $lat . ' ) ) * SIN( RADIANS( lat ) ) ) ) AS distance';
    }

}