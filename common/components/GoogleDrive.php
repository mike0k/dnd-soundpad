<?php

namespace common\components;

use common\models\DbVar;
use yii\helpers\ArrayHelper;
use yii;

/**
 * Class GoogleDrive
 * @package common\components
 *
 * @property \Google_Client $client
 * @property \Google_Service_Drive $service
 */
class GoogleDrive extends Component {
    private $client;
    private $service;

    public function init(){
        $this->client = new \Google_Client();
        if (php_sapi_name() == 'cli') {
            //throw new Exception('This application must be run on the command line. '.php_sapi_name().' is not allowed.');
            $this->client->setAuthConfig(Yii::getAlias('@media').'/token/google-console-private-key.json');
        }else{
            //$this->client->setRedirectUri('https://d9e3a2269b5d.ngrok.io/life/api/finance/index');
            $this->client->setRedirectUri('http://localhost/life/api/auth/google');
            $this->client->setClientId('18557198167-f2trifpo7saat2jr66rk3ft7qsafa2jq.apps.googleusercontent.com');
            $this->client->setClientSecret('KP8OQjj9saEuNqO-Zlr2zuMO');
        }

        $this->client->setScopes([\Google_Service_Drive::DRIVE]);
        $this->client->setSubject('michael.kirkbright@gmail.com');
    }

    private function connect () {
        if (php_sapi_name() != 'cli') {
            //throw new Exception('This application must be run on the command line. '.php_sapi_name().' is not allowed.');
        }

        $token = DbVar::find()->where(['name' => 'google-auth-token'])->one();
        if (!empty($token)) {
            if(empty($token->data['token']) && !empty($token->data['code'])) {
                //after manually approving the app, you are redirected with a code. this converts the code into a token
                $accessToken = $this->client->fetchAccessTokenWithAuthCode($token->data['code']);
                $this->client->setAccessToken($accessToken);
                $this->updateToken();
            }else if(!empty($token->data['token'])){
                //this applies the cached token
                $this->client->setAccessToken($token->data['token']);
            }
        }

        // If there is no previous token or it's expired.
        if ($this->client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                $this->updateToken();
            } else {
                // Request authorization from the user.
                $authUrl = $this->client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                exit;
            }
        }
    }

    public function updateToken(){
        DbVar::add('google-auth-token', ['token' => $this->client->getAccessToken()]);
    }

    public function listDocs () {
        $this->init();
        $this->connect();
        $this->service = new \Google_Service_Drive($this->client);

        // Print the names and IDs for up to 10 files.
        $optParams = array(
            'pageSize' => 1,
            'fields' => 'nextPageToken, files(id, name)'
        );
        $results = $this->service->files->listFiles($optParams);

        //go to google drive "share" link and copy the file id from the url

        //live
        //$results = $this->service->files->get('1GScsuSC8zi0bUft5jEGr9LWdZEglH-JtVXbpBNNybHM');

        //test file
        //$results = $this->service->files->get('1gcPzR8YLoB4QHdJkftr8YGBj68JndDuwpwdLiS0FR5g');
        var_dump($results);
        exit;
    }

    public function incomingJobs () {
        $this->init();
        $this->service = new \Google_Service_Sheets($this->client);
        $results = [];

        //https://docs.google.com/spreadsheets/d/1gcPzR8YLoB4QHdJkftr8YGBj68JndDuwpwdLiS0FR5g/edit?usp=sharing
        $fileIdDev = '1gcPzR8YLoB4QHdJkftr8YGBj68JndDuwpwdLiS0FR5g';
        $fileIdLive = '1GScsuSC8zi0bUft5jEGr9LWdZEglH-JtVXbpBNNybHM';
        $range = 'Incoming Jobs!A1:H300';
        $rows = $this->service->spreadsheets_values->get($fileIdLive, $range);
        $valid = false;
        foreach($rows as $row){
            if($valid){
                $results[] = [
                    'agent' => $row[0],
                    'valuer' => $row[1],
                    'address' => $row[3],
                    'package' => $row[4],
                    'staff' => $row[5],
                    'date' => $row[6],
                    'time' => $row[7],
                ];
            }
            if($row[0] == 'Client'){
                $valid = true;
            }
        }
        DbVar::add('incoming-jobs-bookings', $results);
    }

}