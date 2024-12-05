<?php

namespace common\components;

use Yii;

class Geocode extends Component {

    public function byLatLng ($lat, $lng) {
        $url = $this->getUrl() . '&latlng=' . $lat . ',' . $lng;
        $data = $this->getData($url);

        return $this->formatResults($data);
    }

    public function byPostcode($postcode){
        $postcode = str_replace(' ', '', $postcode);
        $url = $this->getUrl() . '&address=' . $postcode;
        $data = $this->getData($url);

        return $this->formatResults($data);
    }

    private function getUrl(){
        return 'https://maps.google.com/maps/api/geocode/json?key=' . Yii::$app->params['google']['apiKey-server'];
    }

    private function formatResults($data){
        $return = [];
        if (!empty($data->results)) {
            foreach ($data->results as $loc) {
                $item = [];
                foreach ($loc->address_components as $part) {
                    switch ($part->types[0]) {
                        case 'street_number':
                            $item['name'] = $part->long_name;
                            break;
                        case 'route':
                            $item['street'] = $part->long_name;
                            break;
                        case 'locality':
                            $item['area'] = $part->long_name;
                            break;
                        case 'postal_town':
                            $item['town'] = $part->long_name;
                            break;
                        case 'administrative_area_level_2':
                            $item['region'] = $part->long_name;
                            break;
                        case 'postal_code':
                            $item['postcode'] = $part->long_name;
                            break;
                        default:
                    }
                }

                /*if(!empty($item['name']) && !empty($item['street']) && !empty($item['town']) && !empty($item['postcode'])) {
                    $item['lat'] = $loc->geometry->location->lat;
                    $item['lng'] = $loc->geometry->location->lng;
                    $return[] = $item;
                }*/

                if(!empty($item['postcode'])) {
                    $item['lat'] = $loc->geometry->location->lat;
                    $item['lng'] = $loc->geometry->location->lng;
                    $return[] = $item;
                }
            }

            //override - only return the first results since most results are junk
            $return = $return[0];
        }

        return $return;
    }

    private function getData ($url) {
        $return = false;
        $ch = curl_init();

        try {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //curl_setopt($ch, CURLOPT_HEADER, false);

            $output = curl_exec($ch);
        } catch (\Exception $exception) {
            $output = null;
            $return = $exception;
        }

        curl_close($ch);

        if (!empty($output)) {
            $return = json_decode($output);

            if (json_last_error() == JSON_ERROR_NONE) {
                //$return = $output;
            }
        }

        return $return;
    }
}