<?php

namespace common\components;

use common\models\DbDiary;
use common\models\DbMessage;
use common\models\DbUser;
use common\models\DbUserAssign;
use Yii;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class ActiveRecord extends \yii\db\ActiveRecord {

    public $search;
    public $searchSort;

    protected $_refExtension;

    ///////////////////////////////////////
    // ACTIONS
    ///////////////////////////////////////

    public function init () {
        parent::init();
        $this->convertSerial($this->listSerialAttrs(), 'arr');
        $this->convertComma($this->listCommaAttrs(), 'arr');
        if (!empty($_REQUEST[$this->className]['search'])) {
            $this->search = $_REQUEST[$this->className]['search'];
        }
        if (!empty($_REQUEST[$this->className]['searchSort'])) {
            $this->searchSort = $_REQUEST[$this->className]['searchSort'];
        }
    }

    public function beforeDelete () {
        $relations = $this->listDeleteRelations();
        if (!empty($relations)) {
            foreach ($relations as $relation) {
                if (!empty($this->$relation)) {
                    if (is_array($this->$relation)) {
                        foreach ($this->$relation as $temp) {
                            $temp->delete();
                        }
                    } else {
                        $this->$relation->delete();
                    }
                }
            }
        }

        return parent::beforeDelete();
    }

    public function afterFind () {
        parent::afterFind();
        $this->convertSerial($this->listSerialAttrs(), 'arr');
        $this->convertComma($this->listCommaAttrs(), 'arr');
    }

    public function beforeValidate () {
        $this->convertTime($this->listTimeAttrs(), 'int');
        $this->convertPrice($this->listPriceAttrs(), 'int');
        $this->setDefaults();
        $this->convertSerial($this->listSerialAttrs(), 'str');
        $this->convertComma($this->listCommaAttrs(), 'str');

        return parent::beforeValidate();
    }

    public function afterValidate () {
        parent::afterValidate();
        $this->convertSerial($this->listSerialAttrs(), 'arr');
        $this->convertComma($this->listCommaAttrs(), 'arr');
    }

    public function beforeSave ($insert) {
        $this->convertSerial($this->listSerialAttrs(), 'str');
        $this->convertComma($this->listCommaAttrs(), 'str');

        return parent::beforeSave($insert);
    }

    public function afterSave ($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        $this->convertSerial($this->listSerialAttrs(), 'arr');
        $this->convertComma($this->listCommaAttrs(), 'arr');
    }

    public static function find () {
        return new ActiveQuery(get_called_class());
    }

    public function setDefaults () {
        if ($this->hasAttribute('addedById') && empty($this->addedById) && !empty(Yii::$app->user) && !Yii::$app->user->isGuest) {
            $this->addedById = Yii::$app->user->id;
        }

        if ($this->hasAttribute('ipAddress') && empty($this->ipAddress)) {
            $this->ipAddress = $_SERVER['REMOTE_ADDR'];
        }

        /*if ($this->hasAttribute('reference') && empty($this->reference)) {
            $this->reference = $this->genReference();
        }*/

        if ($this->hasAttribute('created') && empty($this->created)) {
            $this->created = time();
        }

        if ($this->hasAttribute('updated')) {
            $this->updated = time();
        }
    }

    ///////////////////////////////////////
    // ATTRIBUTES
    ///////////////////////////////////////

    public function getClassName () {
        return (new \ReflectionClass($this))->getShortName();
    }

    public static function getModel($name){
        $prefix = 'common\models\\';
        if(strpos(self::className(), 'api\models') !== false && class_exists('api\models\\'.$name)){
            $prefix = 'api\models\\';
        }

        if (strpos($name, '\\') === false) {
            $name = $prefix . $name;
        }

        return new $name;
    }

    public function getSearchAttrs () {
        return array();
    }

    public function getSearchOrder () {
        return ['created DESC'];
    }

    public function getTimeDiff () {
        if ($this->hasAttribute('startTime') && $this->hasAttribute('endTime') && !empty($this->startTime) && !empty($this->endTime)) {
            return $this->endTime - $this->startTime;
        }

        return 0;
    }

    public function getTimeLength() {
        return time() - $this->timeDiff;
    }

    ///////////////////////////////////////
    // RELATIONS
    ///////////////////////////////////////

    protected function deleteRelations ($relations) {
        if (!empty($relations)) {
            foreach ($relations as $relation) {
                if (!empty($this->$relation)) {
                    if (is_array($this->$relation)) {
                        foreach ($this->$relation as $model) {
                            $model->delete();
                        }
                    } else {
                        $this->$relation->delete();
                    }
                }
            }
        }
    }

    public function getAddedBy () {
        if ($this->hasAttribute('addedById')) {
            return $this->hasOne(DbUser::className(), ['id' => 'addedById']);
        }
    }

    public function getMessageBoard () {
        return $this->hasMany(self::className(), ['refId' => 'id'])
            ->onCondition(['refType' => $this->getClassName()]);
    }

    public function getRefExtension () {
        return $this->hasOne(self::className(), ['id' => 'id'])
            ->alias($this->tableName().'2')
            ->onCondition([
                $this->tableName().'2.refType' => $this->_refExtension,
            ]);
    }

    public function getRef ($refType = null) {
        $refType = (empty($refType) ? $this->refType : $refType);

        if (empty($refType)) {
            return false;
        }

        $model = $this->getModel($refType);
        $this->_refExtension = str_replace('common\models\\', '', $refType);

        return $this->hasOne($model->className(), ['id' => 'refId'])
            ->via('refExtension');
    }

    public function getUserAssign () {
        return $this->hasMany(DbUserAssign::className(), ['refId' => 'id'])
            ->onCondition(['refType' => $this->getClassName()]);
    }

    ///////////////////////////////////////
    // GENERATOR
    ///////////////////////////////////////

    /**
     * @param string $str
     * @return string
     */
    public static function encryption ($str) {
        $salt = Yii::$app->params['publicSalt'];
        $encryption = base64_encode(md5($salt . md5(hash('SHA512', $str . $salt) . $salt)));

        return $encryption;
    }

    /**
     * @param string $prefix
     * @param string $affix
     * @param int $count
     * @return string
     */
    public function genReference ($prefix = '', $affix = '', $count = 0) {
        if ($count > 1000) {
            return '';
        }
        $ref = $prefix . rand(100000, 999999) . $affix;
        $exists = $this->find()->where(array('reference' => $ref))->one();
        if (!empty($exists)) {
            $count++;
            $ref = $this->genReference($prefix, $affix, $count);
        }

        return $ref;
    }

    /**
     * @param string $attribute
     * @param int $length
     * @return string
     */
    public function generateRandomString ($attribute, $length = 32) {
        $randomString = Yii::$app->getSecurity()->generateRandomString($length);

        if (!$this->findOne([$attribute => $randomString])) {
            return $randomString;
        } else {
            return $this->generateRandomString($attribute, $length);
        }
    }

    ///////////////////////////////////////
    // CALCULATION
    ///////////////////////////////////////

    /**
     * Calculates the great-circle distance between two points, with the Vincenty formula.
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @return float Distance between points in [m] (same as earthRadius)
     */
    public static function calcDistance ($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo) {
        $earthRadius = 6371000; //Mean earth radius in [m]

        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);

        return $angle * $earthRadius;
    }

    /**
     * @param $val
     * @return float
     */
    public function calcTax($val){
        $val = !empty($val) ? $val : 0;
        return round($val * Yii::$app->params['vat'], 2, PHP_ROUND_HALF_UP);
    }

    public function isChanged($attr, $allowEmpty = false){
        $valid = false;
        if($this->hasAttribute($attr)){
            if($allowEmpty || (!empty($this->oldAttributes) && !empty($this->oldAttributes[$attr]) && !empty($this->$attr))) {
                if(empty($this->oldAttributes) && !empty($this->$attr)){
                    $valid = true;
                } else if(!empty($this->oldAttributes) &&$this->$attr != $this->oldAttributes[$attr]){
                    $valid = true;
                }
            }
        }

        return $valid;
    }

    /*
     * @param string $status //new status
     * @param string $test //current status
     */
    public function isStatusAfter ($status, $test = null) {
        $test = (!empty($test) ? $test : $this->status);
        foreach ($this->listStatus() as $key => $val) {
            if ($key == $status) {
                return false;
            }
            if ($key == $test) {
                return true;
            }
        }
    }

    ///////////////////////////////////////
    // COMMA SEPARATED & SERIALIZED
    ///////////////////////////////////////

    /**
     * @param string $attr
     * @param array|string|int $values
     */
    public function addComma ($attr, $values) {
        if (!is_array($values)) {
            $values = array($values);
        }
        if (!is_array($this->$attr)) {
            $this->convertComma($attr, 'arr');
        }
        $this->$attr = ArrayHelper::merge($this->$attr, $values);

        /*$temp = array();
        foreach ($this->$attr as $val) {
            if (!in_array($val, $temp) && $val != 'none') {
                $temp[] = $val;
            }
        }
        foreach ($values as $val) {
            if (!in_array($val, $temp)) {
                $temp[] = $val;
            }
        }
        sort($temp);
        $this->$attr = $temp;*/
    }

    /**
     * @param string $attr
     * @param array|string|int $values
     * @param bool $overwriteExisting
     */
    public function addSerial ($attr, $values, $overwriteExisting = true) {
        if (!is_array($this->$attr)) {
            $this->convertSerial($attr, 'arr');
        }
        if (!is_array($values)) {
            $values = array($values);
        }
        $this->$attr = ArrayHelper::merge($this->$attr, $values);

        /*$temp = array();
        foreach ($this->$attr as $key => $val) {
            if (!array_key_exists($key, $temp)) {
                $temp[$key] = $val;
            }
        }
        foreach ($values as $key => $val) {
            if ($overwriteExisting) {
                if (!array_key_exists($key, $temp) || (array_key_exists($key, $temp) && $temp[$key] != $val)) {
                    $temp[$key] = $val;
                }
            } else {
                if (!array_key_exists($key, $temp)) {
                    $temp[$key] = $val;
                }
            }
        }
        $this->$attr = $temp;*/
    }

    /**
     * @param string|array $attrs
     * @param string $format
     * @return array|string
     */
    public function convertComma ($attrs, $format = 'str', $separator = ',') {
        if (!empty($attrs)) {
            if (!is_array($attrs)) {
                $attrs = array($attrs);
            }
            foreach ($attrs as $attr) {
                if (empty($this->$attr)) {
                    $this->$attr = array();
                }
                if ($format == 'str' && is_array($this->$attr)) {
                    $result = array();
                    foreach ($this->$attr as $key => $val) {
                        if (!empty($val) || $val == 0) {
                            $result[] = trim($val);
                        }
                    }
                    sort($result);
                    $this->$attr = $separator . implode($separator, $result) . $separator;
                    if ($this->$attr == $separator . $separator) {
                        $this->$attr = '';
                    }
                } elseif ($format == 'arr' && !is_array($this->$attr)) {
                    $result = array();
                    $temp = explode($separator, str_replace(', ', ',', $this->$attr));
                    foreach ($temp as $key => $val) {
                        if (!empty($val) || $val === 0) {
                            $result[] = trim($val);
                        }
                    }
                    sort($result);
                    $this->$attr = $result;
                }
            }
        }
    }

    /**
     * @param string|array $attrs
     * @param string $format
     * @return array|string
     */
    public function convertSerial ($attrs, $format = 'str') {
        if (!empty($attrs)) {
            if (!is_array($attrs)) {
                $attrs = array($attrs);
            }
            foreach ($attrs as $attr) {
                if ($this->hasAttribute($attr)) {
                    if (empty($this->$attr)) {
                        $this->$attr = array();
                    }
                    if ($format == 'str' && is_array($this->$attr)) {
                        $this->$attr = serialize($this->$attr);
                    } elseif ($format == 'arr' && !is_array($this->$attr)) {
                        $temp = unserialize($this->$attr);
                        $this->$attr = $temp;
                    }
                }
            }
        }
    }

    /**
     * @param string|array $attrs
     * @param string $convertTo
     * @param string $strFormat
     * @return int|string
     */
    public function convertTime ($attrs, $convertTo = 'int', $strFormat = 'datetime') {
        if (!empty($attrs)) {
            if (!is_array($attrs)) {
                $attrs = array($attrs);
            }
            foreach ($attrs as $attr) {
                if ($this->hasAttribute($attr) && !empty($this->$attr)) {
                    if ($convertTo == 'int' && !is_numeric($this->$attr)) {
                        $this->$attr = strtotime(str_replace('/', '-', $this->$attr));
                        //check if strtotime() formatted correctly
                        if (empty($this->$attr) || !is_numeric($this->$attr) || $this->$attr < strtotime('1 Jan 2000')) {
                            $this->$attr = null;
                        }
                    } elseif ($convertTo == 'str' && is_numeric($this->$attr)) {
                        if ($strFormat == 'date') {
                            $this->$attr = Yii::$app->formatter->asDate($this->$attr);
                        } else {
                            $this->$attr = Yii::$app->formatter->asDatetime($this->$attr);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string|array $attrs
     * @param string $convertTo
     * @return int|string
     */
    public function convertPrice ($attrs, $convertTo = 'int') {
        if (!empty($attrs)) {
            if (!is_array($attrs)) {
                $attrs = array($attrs);
            }
            foreach ($attrs as $attr) {
                if ($this->hasAttribute($attr) && !empty($this->$attr)) {
                    if ($convertTo == 'int' && strpos($this->$attr, ',') !== false) {
                        $this->$attr = str_replace(',', '', $this->$attr);
                    } elseif ($convertTo == 'str' && strpos($this->$attr, ',') === false) {
                        $this->$attr = Yii::$app->formatter->asMoney($this->$attr);
                    }
                }
            }
        }
    }

    /**
     * @param string $attr
     * @param array|string|int $values
     * @param bool $allowEmpty
     */
    public function deleteComma ($attr, $values, $allowEmpty = true) {
        if (!is_array($values)) {
            $values = array($values);
        }
        if (!is_array($this->$attr)) {
            $this->convertComma($attr, 'arr');
        }
        $temp = array();
        foreach ($this->$attr as $val) {
            if (!in_array($val, $temp) && !in_array($val, $values)) {
                $temp[] = $val;
            }
        }
        if (!$allowEmpty && empty($temp)) {
            //insert 'none' if no other account types are present
            $temp[] = 'none';
        }
        $this->$attr = $temp;
    }

    /**
     * @param string $attr
     * @param array|string|int $values
     */
    public function deleteSerial ($attr, $values) {
        if (!is_array($values)) {
            $values = array($values);
        }
        if (!is_array($this->$attr)) {
            $this->convertSerial($attr, 'arr');
        }
        $temp = array();
        foreach ($this->$attr as $key => $val) {
            if (!array_key_exists($key, $temp) && !in_array($key, $values)) {
                $temp[$key] = $val;
            }
        }
        $this->$attr = $temp;
    }


    ///////////////////////////////////////
    // DIARY
    ///////////////////////////////////////

    /**
     * @param int $start
     * @param int $end
     * @return DbDiary[]
     */
    public function fetchDiaryItems ($start = null, $end = null) {
        $items = array();
        $query = DbDiary::find();

        $start = (!empty($start) ? $start : time());
        $start = (!is_numeric($start) ? strtotime($start) : $start);

        $end = (!empty($end) ? $end : time());
        $end = (!is_numeric($end) ? strtotime($end) : $end);

        $query->whereTimeRange($start, $end);

        if (!empty($this->id)) {
            $query->andWhere([
                'refId' => $this->id,
                'refType' => $this->className,
            ]);
        }

        if ($this->className == 'DbDiary') {
            if(!empty($this->type)) {
                $query->andWhere([
                    'type' => $this->type,
                ]);
            }
            if(!empty($this->propId)) {
                $query->andWhere([
                    'propId' => $this->propId,
                ]);
            }
        }

        $temp = $query->all();
        if (!empty($temp)) {
            $items = $temp;
        }

        return $items;
    }

    /**
     * @param int $start
     * @param int $end
     * @return array
     */
    public function renderDiaryItems ($start = null, $end = null) {
        $start = (empty($start) ? $this->startTime : $start);
        $end = (empty($end) ? $this->endTime : $end);
        $items = $this->fetchDiaryItems($start, $end);
        //var_dump(date('Y-m-d', $start),$items);exit;
        $items = DbDiary::instance()->formatDiaryItems($items);

        return $items;
    }

    ///////////////////////////////////////
    // LISTS
    ///////////////////////////////////////

    /**
     * @param string $listName
     * @param int|string $key
     * @return string
     */
    public function getListLabel ($listName, $key = null) {
        if(!empty($key) && is_array($key)){
            $label = [];
            foreach ($key as $item){
                $label[] = $this->getListLabel($listName, $item);
            }
            return implode(', ', $label);
        }

        $label = '';
        $list = $this->$listName();
        if (!empty($list)) {
            if (empty($key)) {
                $temp = lcfirst(str_replace('list', '', $listName));
                if($this->hasAttribute($temp)) {
                    $key = $this->$temp;
                }
            }
            foreach ($list as $itemKey => $itemVal) {
                if ($key == $itemKey) {
                    $label = $itemVal;
                }
            }
        }

        return $label;
    }

    public function listBoolean () {
        return [
            0 => 'No',
            1 => 'Yes',
        ];
    }

    public function listCommaAttrs () {
        return [];
    }

    public function listDeleteRelations () {
        return [

        ];
    }

    public function listPriceAttrs () {
        return [];
    }

    public function listSerialAttrs () {
        return [
            'data',
        ];
    }

    public function listSearchSort () {
        return [
            'created-asc' => 'created ASC',
            'created-desc' => 'created DESC',
            'updated-asc' => 'updated ASC',
            'updated-desc' => 'updated DESC',
        ];
    }

    public function listStatus () {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ];
    }

    public function listTimeAttrs () {
        return [
            'created',
            'updated',
            'startTime',
            'endTime',
        ];
    }

    ///////////////////////////////////////
    // CLEAN
    ///////////////////////////////////////

    protected static function cleanNull($data, $count = 0){
        $return = [];
        $count++;
        if($count < 1000 && !empty($data) && is_array($data)){
            foreach ($data as $key => $val){
                if(is_array($val)){
                    $return[$key] = self::cleanNull($val, $count);
                }else if(is_null($val)){
                    $return[$key] = '';
                }else{
                    $return[$key] = $val;
                }
            }
        }
        return $return;
    }
}