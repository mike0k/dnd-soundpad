<?php

namespace common\components;

use Yii;


class Formatter extends \yii\i18n\Formatter {


    /**
     * implode model address parts or defined array into single string
     * @param mixed   $raw model or array containing address parts
     * @param boolean $lineBreak option to implode address parts by commas or line breaks
     * @return string
     */
    public function asAddress ($raw, $lineBreak = false) {
        $address = '';

        if (!empty($raw)) {
            if (is_object($raw)) {
                $parts = array(
                    //$raw->flat,
                    $raw->name . ' ' . $raw->street,
                    $raw->area,
                    $raw->town,
                    $raw->postcode,
                );
            } elseif (is_array($raw)) {
                $parts = $raw;
            }

            if (!empty($parts)) {
                $duplicates = array();
                foreach ($parts as $part) {
                    if (!empty($part) && $part != ' ' && !in_array(trim($part), $duplicates)) {
                        $part = trim($part);
                        $duplicates[] = $part;
                        if (empty($address)) {
                            $address .= $part;
                        } else {
                            if ($lineBreak) {
                                $address .= "<br />" . $part;
                            } else {
                                $address .= ", " . $part;
                            }
                        }
                    }
                }
            }

        }

        return $address;
    }

    /**
     * calculate length of time between 2 dates
     * @param mixed  $startVal
     * @param mixed  $endVal
     * @param string $type
     * @return string
     */
    public function asAge ($startVal, $endVal = null, $type = 'age') {
        if (is_numeric($startVal)) {
            $start = new \DateTime();
            $start->setTimestamp($startVal);
        } else {
            $start = new \DateTime($startVal);
        }

        if (!empty($endVal)) {
            if (is_numeric($endVal)) {
                $end = new \DateTime();
                $end->setTimestamp($endVal);
            } else {
                $end = new \DateTime($endVal);
            }
        } else {
            $end = new \DateTime();
        }

        $interval = $end->diff($start);
        $doPlural = function ($nb, $str) {
            return $nb > 1 ? $str . 's' : $str;
        }; // adds plurals

        $data = array();
        if ($interval->y > 0) {
            $data[] = array(
                'key' => 'y',
                'val' => '%y',
                'ref' => $doPlural($interval->y, "year")
            );
        }
        if ($interval->m > 0) {
            $data[] = array(
                'key' => 'm',
                'val' => '%m',
                'ref' => $doPlural($interval->m, "month")
            );
        }
        if ($interval->d > 0) {
            $data[] = array(
                'key' => 'd',
                'val' => '%d',
                'ref' => $doPlural($interval->d, "day")
            );
        }
        if ($interval->h > 0) {
            $data[] = array(
                'key' => 'h',
                'val' => '%h',
                'ref' => $doPlural($interval->h, "hour")
            );
        }
        if ($interval->i > 0) {
            $data[] = array(
                'key' => 'i',
                'val' => '%i',
                'ref' => $doPlural($interval->i, "minute")
            );
        }
        if ($interval->s > 0) {
            if (empty($data)) {
                switch ($type) {
                    case 'double':
                        return $interval->s . ' ' . $doPlural($interval->s, "second");

                    case 'age':
                    default:
                        return "less than a minute ago";
                }
            } else {
                $data[] = array(
                    'key' => 's',
                    'val' => '%s',
                    'ref' => $doPlural($interval->s, "second")
                );
            }
        }

        if ($startVal < time()) {
            switch ($type) {
                case 'single':
                case 'ending':
                    $type = 'age';
            }
        } else if ($startVal > time()) {
            switch ($type) {
                case 'age':
                    $type = 'ending';
            }
        }

        if (!empty($data)) {
            switch ($type) {
                case 'age':
                    $msg = $data[0]['val'] . ' ' . $data[0]['ref'] . ' ago';
                    break;
                case 'days':
                    $msg = 0;
                    foreach ($data as $set) {
                        $val = $interval->format($set['val']);
                        switch ($set['key']) {
                            case 'y':
                                $msg += $val * 365;
                                break;
                            case 'm':
                                $msg += $val * 30;
                                break;
                            case 'd':
                                $msg += $val;
                                break;
                        }
                    }
                    break;
                case 'months':
                    $msg = 0;
                    foreach ($data as $set) {
                        $val = $interval->format($set['val']);
                        switch ($set['key']) {
                            case 'y':
                                $msg += $val * 12;
                                break;
                            case 'm':
                                $msg += $val;
                                break;
                        }
                    }
                    break;
                case 'decimal':
                    $msg = 0;
                    foreach ($data as $set) {
                        $val = $interval->format($set['val']);
                        switch ($set['key']) {
                            case 'm':
                                $msg += ($val * 30) * 24;
                                break;
                            case 'd':
                                $msg += $val * 24;
                                break;
                            case 'h':
                                $msg += $val;
                                break;
                            case 'i':
                                $msg += $val * (1 / 60);
                                break;
                        }
                    }
                    break;
                case 'double':
                    $msg = $data[0]['val'] . ' ' . $data[0]['ref'] . ' and ' . $data[1]['val'] . ' ' . $data[1]['ref'];
                    break;
                case 'single':
                    $msg = $data[0]['val'] . ' ' . $data[0]['ref'];
                    break;
                case 'ending':
                    $msg = 'in ' . $data[0]['val'] . ' ' . $data[0]['ref'];
                    break;
                case 'hours':
                    $msg = 0;
                    foreach ($data as $set) {
                        $val = $interval->format($set['val']);
                        switch ($set['key']) {
                            case 'm':
                                $msg += ($val * 30) * 24;
                                break;
                            case 'd':
                                $msg += $val * 24;
                                break;
                            case 'h':
                                $msg += $val;
                                break;
                            case 'i':
                                if ($val > 30) {
                                    $msg += 1;
                                }
                                break;
                        }
                    }
                    break;
                case 'minutes':
                    $msg = 0;
                    foreach ($data as $set) {
                        $val = $interval->format($set['val']);
                        switch ($set['key']) {
                            case 'm':
                                $msg += ($val * 30) * 24 * 60;
                                break;
                            case 'd':
                                $msg += $val * 24 * 60;
                                break;
                            case 'h':
                                $msg += $val * 60;
                                break;
                            case 'i':
                                $msg += $val;
                                break;
                            case 's':
                                if ($val > 30) {
                                    $msg += 1;
                                }
                                break;
                        }
                    }
                    break;
                default:
                    foreach ($data as $set) {
                        if (empty($msg)) {
                            $msg = $set['val'] . ' ' . $set['ref'];
                        } else {
                            $msg .= ', ' . $set['val'] . ' ' . $set['ref'];
                        }
                    }
                //$msg = $data[0]['val'].' '.$data[0]['ref'];
            }
        } else {
            $msg = '';
        }

        return $interval->format($msg);
    }

    /**
     * Converts camelCaseValue to normal value
     * @param string $value
     * @param string $direction
     * @return string
     */
    public function asCamelCase ($value, $direction = 'explode') {
        if ($direction == 'implode') {
            return str_replace(' ', '', ucwords($value));
        } else {
            return ucfirst(implode(" ", preg_split("/(?=[A-Z0-9])/", $value)));
        }
    }

    /**
     * returns a Material Design color
     * @param mixed   $id
     * @param integer $grade
     * @return string
     */
    public function asColor ($id = null, $grade = null) {
        $grade = (!empty($grade) ? $grade : 800);
        $colors = $this->listColors();

        if (!empty($id) && !is_numeric($id) && !empty($colors[$id])) {
            $color = $colors[$id][$grade];
        }

        if (empty($color)) {
            $id = (!empty($id) || $id <= 0 || $id > 18 ? $id : rand(0, 18));
            $i = 0;
            foreach ($colors as $key => $data) {
                if ($i == $id) {
                    $color = $data[$grade];
                    break;
                }
                $i++;
            }
        }

        return (empty($color) ? '000000' : $color);
    }

    /**
     * switch comma separated values between array and string
     * @param string $values - values to be formatted
     * @param string $format - string/array - format to convert the attribute to
     * @param mixed  $separator - character to separate data by
     */
    public function asCommaSeparated ($values, $format = 'str', $separator = ',') {
        if (empty($values)) {
            $values = array();
        }
        if ($format == 'str' && is_array($values)) {
            $result = array();
            foreach ($values as $key => $val) {
                if (!empty($val) || $val == 0) {
                    $result[] = trim($val);
                }
            }
            sort($result);
            $values = $separator . implode($separator, $result) . $separator;
            if ($values == $separator . $separator) {
                $values = '';
            }
        } elseif ($format == 'arr' && !is_array($values)) {
            $result = array();
            $temp = explode($separator, str_replace(', ', ',', $values));
            foreach ($temp as $key => $val) {
                if (!empty($val) || $val === 0) {
                    $result[] = trim($val);
                }
            }
            sort($result);
            $values = $result;
        }

        return $values;
    }

    /**
     * Wraps a pre tag around a var_dump()
     * @param mixed   $data
     * @param boolean $die
     */
    public function asDebug ($data, $die = false) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        if ($die) {
            exit;
        }
    }

    /**
     * calculate and format the distance between 2 set of coordinates
     * @param integer $lat1
     * @param integer $lng1
     * @param integer $lat2
     * @param integer $lng2
     * @param string  $unit - k = kilometers, m = miles, n = nautical miles
     * @return integer
     */
    function asDistance ($lat1, $lng1, $lat2, $lng2, $unit = 'm') {

        $theta = $lng1 - $lng2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == 'k') {
            return ($miles * 1.609344);
        } else if ($unit == 'n') {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }


    /**
     * change filename to a common format for file-system
     * @param string $filename
     * @param string $fileExtension
     * @return string
     */
    public function asFileName ($filename, $fileExtension) {
        //remove extension
        $clean = str_replace('.' . $fileExtension, '', $filename);

        //strip blacklisted chars
        $strip = array("~", "`", "!", "@", "#", "$", "£", "%", "^", "&", "*", "(", ")", "=", "+", "[", "{", "]",
            "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
            "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = trim(str_replace($strip, "", strip_tags($clean)));

        //replace spaces
        $clean = preg_replace('/\s+/', "_", $clean);
        $clean = str_replace('__', "_", $clean);

        //add extension back in and format to lowercase
        $clean = $clean . '.' . $fileExtension;

        //$clean = strtolower($clean . '.' . $fileExtension);

        return $clean;
    }

    /**
     * Converts hex colour to rgb colour
     * @param string $hex
     * @return array
     */
    function asRgb ($hex) {
        if ($hex[0] == '#') {
            $hex = substr($hex, 1);
        }
        if (strlen($hex) == 6) {
            list($r, $g, $b) = array($hex[0] . $hex[1], $hex[2] . $hex[3], $hex[4] . $hex[5]);
        } elseif (strlen($hex) == 3) {
            list($r, $g, $b) = array($hex[0] . $hex[0], $hex[1] . $hex[1], $hex[2] . $hex[2]);
        } else {
            return false;
        }
        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        return array('r' => $r, 'g' => $g, 'b' => $b);
    }

    /**
     * Prepends pound sign and formats the number
     * @param string $value
     * @return string
     */
    public function asMoney ($value, $decimal = 2) {
        if (!is_numeric($value)) {
            if ($value == '') {
                $value = 0;
            } else {
                intval($value);
            }
        }
        if (is_numeric($value)) {
            return number_format(floatval($value), $decimal);
            //return '&pound;' . number_format(floatval($value), $decimal);
            //return '\u00A3' . number_format(floatval($value), $decimal);
        }
    }

    /**
     * Appends the text th, st, nd, or rd to a number based on its value
     * @param integer $number
     * @return string
     */
    function asOrdinalNumber ($number) {
        $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        } else {
            return $number . $ends[$number % 10];
        }
    }

    /*
     * calculates the percentage and returns as int
     * @param int $int1
     * @param int $int2
     * @param boolean $incSymbol
     * @return int
     */
    public function asPercentDiff ($int1, $int2, $incSymbol = false) {
        $result = 0;

        if (!empty($int1) && !empty($int2)) {
            $result = round(($int1 / $int2) * 100);

            /*if ($result > 0) {
                $result = $result - 100;
            }*/
        }

        if ($incSymbol) {
            return $result . '%';
        } else {
            return $result;
        }
    }

    /*
     * format a phone number to a common format
     * @param string $number
     * @return string
     */
    public function asPhone ($number) {
        //remove any spaces before formatting
        $number = trim(str_replace(' ', '', $number));

        // Change the international number format and remove any non-number character
        $number = preg_replace('/[^0-9]+/', '', str_replace("+", "00", $number));

        // This uses full codes from http://www.area-codes.org.uk/formatting.shtml
        $telephoneFormat = array(
            '02' => "3,4,4",
            '03' => "4,3,4",
            '05' => "3,4,4",
            '0500' => "4,6",
            '07' => "5,6",
            '070' => "3,4,4",
            '076' => "3,4,4",
            '07624' => "5,6",
            '08' => "4,3,4", // some 0800 numbers are 4,6
            '09' => "4,3,4",
            '01' => "5,6", // some 01 numbers are 5,5
            '011' => "4,3,4",
            '0121' => "4,3,4",
            '0131' => "4,3,4",
            '0141' => "4,3,4",
            '0151' => "4,3,4",
            '0161' => "4,3,4",
            '0191' => "4,3,4",
            '013873' => "6,5",
            '015242' => "6,5",
            '015394' => "6,5",
            '015395' => "6,5",
            '015396' => "6,5",
            '016973' => "6,5",
            '016974' => "6,5",
            '016977' => "6,5",
            '0169772' => "6,4",
            '0169773' => "6,4",
            '017683' => "6,5",
            '017684' => "6,5",
            '017687' => "6,5",
            '019467' => "6,5"
        );
        //$telephoneFormat= array_reverse($telephoneFormat);

        // Sorts into longest key first
        $keys = array_map('strlen', array_keys($telephoneFormat));
        array_multisort($keys, SORT_DESC, $telephoneFormat);

        $format = "6,5";
        foreach ($telephoneFormat AS $key => $value) {
            if (substr($number, 0, strlen($key)) == $key) {
                $format = $value;
                break;
            }
        }
        $format = explode(',', $format);

        // Turn number into array based on Telephone Format
        $start = 0;
        $numberArray = array();
        if (!empty($format) && is_array($format)) {
            foreach ($format AS $value) {
                $numberArray[] = substr($number, $start, $value);
                $start = $start + $value;
            }
        }

        // Add brackets around first split of numbers if number starts with 01 or 02
        /*if(!empty($numberArray)){
            if (substr($number,0,2)=="01" || substr($number,0,2)=="02"){
                $numberArray[0]="(".$numberArray[0].")";
            }
        }*/

        // Convert array back into string, split by spaces
        $formattedNumber = implode(" ", $numberArray);

        //revert back to original number if formatting failed
        if (empty($formattedNumber)) {
            $formattedNumber = $number;
        }

        return $formattedNumber;
    }

    /**
     * change filename to a common format for file-system
     * @param string $filename
     * @param string $fileExtension
     * @return string
     */
    public function asSafeFile ($filename, $fileExtension) {
        //remove extension
        $clean = str_replace('.' . $fileExtension, '', $filename);

        //strip blacklisted chars
        $strip = array("~", "`", "!", "@", "#", "$", "£", "%", "^", "&", "*", "(", ")", "=", "+", "[", "{", "]",
            "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
            "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = trim(str_replace($strip, "", strip_tags($clean)));

        //replace spaces
        $clean = preg_replace('/\s+/', "_", $clean);
        $clean = str_replace('__', "_", $clean);

        //add extension back in and format to lowercase
        $clean = $clean . '.' . $fileExtension;

        //$clean = strtolower($clean . '.' . $fileExtension);

        return $clean;
    }

    /**
     * reface url unfriendly chars with readable url friendly chars
     * @param string $string
     * @return string
     */
    public function asSafeUrl ($string) {
        $string = strtolower($string); //convert to lower case
        $string = str_replace(',', '-', $string); //replace commas
        $string = str_replace('/', '-', $string); //replace slashes
        $string = str_replace('  ', ' ', $string); //replace double spacing
        $string = str_replace(' ', '-', $string); //replace spaces
        $string = str_replace('---', '-', $string); //replace triple dash
        $string = str_replace('--', '-', $string); //replace double dash

        return $string;
    }

    /**
     * @param string|array $attrs
     * @param string       $format
     * @return array|string
     */
    public function asSerial ($values, $format = 'str') {
        if (empty($values)) {
            $values = [];
        }

        if ($format == 'str' && is_array($values)) {
            $values = serialize($values);
        } elseif ($format == 'arr' && !is_array($values)) {
            $values = unserialize($values);
        }

        return $values;
    }

    /**
     * Toggles value between DatePicker and timestamp formats
     * @param        string /integer $value
     * @param string $convertTo - integer/string
     * @param string $strFormat - date/\DateTime
     * @return string
     */
    public function asTimestamp ($value, $convertTo = 'int', $strFormat = '\DateTime') {
        if (!empty($value)) {
            if ($convertTo == 'int' && !is_numeric($value)) {
                $value = strtotime(str_replace('/', '-', $value));
                //check if strtotime() formatted correctly
                if (empty($value) || !is_numeric($value) || $value < strtotime('1 Jan 1980')) {
                    $value = null;
                }
            } elseif ($convertTo == 'str' && is_numeric($value)) {
                if ($strFormat == 'date') {
                    $value = Yii::$app->format->date($value);
                } else {
                    $value = Yii::$app->format->dateTime($value);
                }
            } else {
                $value = null;
            }
        }

        return $value;
    }

    /*
     * Calculate and round up VAT
     * @param integer $value - price to be calculated
     * @return integer
     */
    public function asVat ($value) {
        return (ceil(($value * Yii::app()->params['vat']) * 1000) / 1000);
    }

    /*
     * Get the first number of words from a string
     * @param string $string - the text to shortened
     * @param integer $wordCount - number of words to return
     * @return string
     */
    public function asWordLength ($string, $wordCount = 10) {
        return implode(' ', array_slice(explode(' ', $string), 0, $wordCount));
    }

    private function listColors ($order = null) {
        if (empty($order)) {
            $order = array(
                'blue',
                'cyan',
                'green',
                'light-green',
                'yellow',
                'orange',
                'red',
                'pink',
                'deep-purple',
                'indigo',

                'purple',
                'light-blue',
                'teal',
                'lime',
                'amber',
                'deep-orange',
                'brown',
                'grey',
                'blue-grey',
            );
        }

        $colours = array(
            'red' => array('50' => 'FFEBEE', '100' => 'FFCDD2', '200' => 'EF9A9A', '300' => 'E57373', '400' => 'EF5350', '500' => 'F44336', '600' => 'E53935', '700' => 'D32F2F', '800' => 'C62828', '900' => 'B71C1C', 'A100' => 'FF8A80', 'A200' => 'FF5252', 'A400' => 'FF1744', 'A700' => 'D50000',),
            'pink' => array('50' => 'FCE4EC', '100' => 'F8BBD0', '200' => 'F48FB1', '300' => 'F06292', '400' => 'EC407A', '500' => 'E91E63', '600' => 'D81B60', '700' => 'C2185B', '800' => 'AD1457', '900' => '880E4F', 'A100' => 'FF80AB', 'A200' => 'FF4081', 'A400' => 'F50057', 'A700' => 'C51162',),
            'purple' => array('50' => 'F3E5F5', '100' => 'E1BEE7', '200' => 'CE93D8', '300' => 'BA68C8', '400' => 'AB47BC', '500' => '9C27B0', '600' => '8E24AA', '700' => '7B1FA2', '800' => '6A1B9A', '900' => '4A148C', 'A100' => 'EA80FC', 'A200' => 'E040FB', 'A400' => 'D500F9', 'A700' => 'AA00FF',),
            'deep-purple' => array('50' => 'EDE7F6', '100' => 'D1C4E9', '200' => 'B39DDB', '300' => '9575CD', '400' => '7E57C2', '500' => '673AB7', '600' => '5E35B1', '700' => '512DA8', '800' => '4527A0', '900' => '311B92', 'A100' => 'B388FF', 'A200' => '7C4DFF', 'A400' => '651FFF', 'A700' => '6200EA',),
            'indigo' => array('50' => 'E8EAF6', '100' => 'C5CAE9', '200' => '9FA8DA', '300' => '7986CB', '400' => '5C6BC0', '500' => '3F51B5', '600' => '3949AB', '700' => '303F9F', '800' => '283593', '900' => '1A237E', 'A100' => '8C9EFF', 'A200' => '536DFE', 'A400' => '3D5AFE', 'A700' => '304FFE',),
            'blue' => array('50' => 'E3F2FD', '100' => 'BBDEFB', '200' => '90CAF9', '300' => '64B5F6', '400' => '42A5F5', '500' => '2196F3', '600' => '1E88E5', '700' => '1976D2', '800' => '1565C0', '900' => '0D47A1', 'A100' => '82B1FF', 'A200' => '448AFF', 'A400' => '2979FF', 'A700' => '2962FF',),
            'light-blue' => array('50' => 'E1F5FE', '100' => 'B3E5FC', '200' => '81D4FA', '300' => '4FC3F7', '400' => '29B6F6', '500' => '03A9F4', '600' => '039BE5', '700' => '0288D1', '800' => '0277BD', '900' => '01579B', 'A100' => '80D8FF', 'A200' => '40C4FF', 'A400' => '00B0FF', 'A700' => '0091EA',),
            'cyan' => array('50' => 'E0F7FA', '100' => 'B2EBF2', '200' => '80DEEA', '300' => '4DD0E1', '400' => '26C6DA', '500' => '00BCD4', '600' => '00ACC1', '700' => '0097A7', '800' => '00838F', '900' => '006064', 'A100' => '84FFFF', 'A200' => '18FFFF', 'A400' => '00E5FF', 'A700' => '00B8D4',),
            'teal' => array('50' => 'E0F2F1', '100' => 'B2DFDB', '200' => '80CBC4', '300' => '4DB6AC', '400' => '26A69A', '500' => '009688', '600' => '00897B', '700' => '00796B', '800' => '00695C', '900' => '004D40', 'A100' => 'A7FFEB', 'A200' => '64FFDA', 'A400' => '1DE9B6', 'A700' => '00BFA5',),
            'green' => array('50' => 'E8F5E9', '100' => 'C8E6C9', '200' => 'A5D6A7', '300' => '81C784', '400' => '66BB6A', '500' => '4CAF50', '600' => '43A047', '700' => '388E3C', '800' => '2E7D32', '900' => '1B5E20', 'A100' => 'B9F6CA', 'A200' => '69F0AE', 'A400' => '00E676', 'A700' => '00C853',),
            'light-green' => array('50' => 'F1F8E9', '100' => 'DCEDC8', '200' => 'C5E1A5', '300' => 'AED581', '400' => '9CCC65', '500' => '8BC34A', '600' => '7CB342', '700' => '689F38', '800' => '558B2F', '900' => '33691E', 'A100' => 'CCFF90', 'A200' => 'B2FF59', 'A400' => '76FF03', 'A700' => '64DD17',),
            'lime' => array('50' => 'F9FBE7', '100' => 'F0F4C3', '200' => 'E6EE9C', '300' => 'DCE775', '400' => 'D4E157', '500' => 'CDDC39', '600' => 'C0CA33', '700' => 'AFB42B', '800' => '9E9D24', '900' => '827717', 'A100' => 'F4FF81', 'A200' => 'EEFF41', 'A400' => 'C6FF00', 'A700' => 'AEEA00',),
            'yellow' => array('50' => 'FFFDE7', '100' => 'FFF9C4', '200' => 'FFF59D', '300' => 'FFF176', '400' => 'FFEE58', '500' => 'FFEB3B', '600' => 'FDD835', '700' => 'FBC02D', '800' => 'F9A825', '900' => 'F57F17', 'A100' => 'FFFF8D', 'A200' => 'FFFF00', 'A400' => 'FFEA00', 'A700' => 'FFD600',),
            'amber' => array('50' => 'FFF8E1', '100' => 'FFECB3', '200' => 'FFE082', '300' => 'FFD54F', '400' => 'FFCA28', '500' => 'FFC107', '600' => 'FFB300', '700' => 'FFA000', '800' => 'FF8F00', '900' => 'FF6F00', 'A100' => 'FFE57F', 'A200' => 'FFD740', 'A400' => 'FFC400', 'A700' => 'FFAB00',),
            'orange' => array('50' => 'FFF3E0', '100' => 'FFE0B2', '200' => 'FFCC80', '300' => 'FFB74D', '400' => 'FFA726', '500' => 'FF9800', '600' => 'FB8C00', '700' => 'F57C00', '800' => 'EF6C00', '900' => 'E65100', 'A100' => 'FFD180', 'A200' => 'FFAB40', 'A400' => 'FF9100', 'A700' => 'FF6D00',),
            'deep-orange' => array('50' => 'FBE9E7', '100' => 'FFCCBC', '200' => 'FFAB91', '300' => 'FF8A65', '400' => 'FF7043', '500' => 'FF5722', '600' => 'F4511E', '700' => 'E64A19', '800' => 'D84315', '900' => 'BF360C', 'A100' => 'FF9E80', 'A200' => 'FF6E40', 'A400' => 'FF3D00', 'A700' => 'DD2C00',),
            'brown' => array('50' => 'EFEBE9', '100' => 'D7CCC8', '200' => 'BCAAA4', '300' => 'A1887F', '400' => '8D6E63', '500' => '795548', '600' => '6D4C41', '700' => '5D4037', '800' => '4E342E', '900' => '3E2723',),
            'grey' => array('50' => 'FAFAFA', '100' => 'F5F5F5', '200' => 'EEEEEE', '300' => 'E0E0E0', '400' => 'BDBDBD', '500' => '9E9E9E', '600' => '757575', '700' => '616161', '800' => '424242', '900' => '212121',),
            'blue-grey' => array('50' => 'ECEFF1', '100' => 'CFD8DC', '200' => 'B0BEC5', '300' => '90A4AE', '400' => '78909C', '500' => '607D8B', '600' => '546E7A', '700' => '455A64', '800' => '37474F', '900' => '263238',),
        );

        $list = array();
        foreach ($order as $val) {
            $list[$val] = $colours[$val];
        }

        return $list;
    }

}