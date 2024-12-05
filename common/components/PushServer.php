<?php

namespace common\components;

use consik\yii2websocket\events\WSClientMessageEvent;
use consik\yii2websocket\WebSocketServer;

class PushServer extends WebSocketServer {

    public function init () {
        parent::init();

        $this->on(self::EVENT_CLIENT_MESSAGE, function (WSClientMessageEvent $e) {
            $e->client->send($e->message);
        });
    }

    protected function getCommand (ConnectionInterface $from, $msg) {
        return $msg;
    }

    private function commandPing (ConnectionInterface $client, $msg) {
        return $client->send('Pong');
    }

}