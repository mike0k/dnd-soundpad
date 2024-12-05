<?php

namespace common\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "db_user_token".
 *
 * @property string $token
 * @property string $type
 * @property integer $userId
 * @property integer $expire
 * @property integer $created
 *
 * @property DbUser  $user
 */
class DbUserToken extends \common\components\ActiveRecord {

    private $tokenDuration = '3 day';
    private $tokenMaxDuration = '3 month';

    /**
     * @inheritdoc
     */
    public static function tableName () {
        return 'db_user_token';
    }

    /**
     * @inheritdoc
     */
    public function rules () {
        return [
            [['token', 'type', 'userId', 'expire', 'created'], 'required'],
            [['userId', 'expire', 'created'], 'integer'],
            [['token'], 'string'],
            [['token'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels () {
        return [
            'token' => 'Access Token',
            'type' => 'Type',
            'userId' => 'User ID',
            'expire' => 'Expiry Date',
            'created' => 'Creation Date',
        ];
    }

    public static function add($userId){
        if(is_object($userId)){
            $userId = $userId->id;
        }
        $model = new DbUserToken();
        $model->userId = $userId;

        return ($model->save() ? $model->token : false);
    }

    public static function checkAccess($token, $updateExpiry = true){
        $userId = null;
        if(!empty($token)){
            $model = DbUserToken::find()->where([
                'token' => $token,
                'type' => 'api',
            ])->one();
            if(!empty($model)){
                if($model->expire < time()){
                    $model->delete();
                }else{
                    if($updateExpiry) {
                        //extend token life if used again within expiry time. Don't allow token life to extend further than max lifespan
                        $model->expire = strtotime('+' . $model->tokenDuration);
                        if ($model->expire < strtotime('+' . $model->tokenMaxDuration, $model->created)) {
                            $model->save();
                        }
                    }
                    $userId = $model->userId;
                }
            }
        }

        return $userId;
    }

    private function genToken ($loop = 0) {
        $token = '';
        if ($loop < 1000 && empty($this->token)) {
            $token = Yii::$app->security->generateRandomString();
            $exists = DbUserToken::find()->where(['token' => $token])->count();
            if(!empty($exists)){
                $loop++;
                $token = $this->genToken($loop);
            }
        }
        return $token;
    }

    public function getUser () {
        return $this->hasOne(DbUser::className(), ['id' => 'userId']);
    }

    public function getSearchOrder () {
        return [
            'expire DESC',
        ];
    }

    public function listType(){
        return [
            'api' => 'API Login',
            'payment' => 'Stripe Payment',
        ];
    }

    public function setDefaults () {
        if (empty($this->created)) {
            $this->created = time();
        }

        if (empty($this->expire)) {
            $this->expire = strtotime('+'.$this->tokenDuration);
        }

        if (empty($this->token)) {
            $this->token = $this->genToken();
        }

        if(empty($this->type)){
            $this->type = 'api';
        }
    }



}
