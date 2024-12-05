<?php

namespace common\components;

use yii\helpers\VarDumper;
use yii\log\Logger;

class Log extends \yii\log\FileTarget {

    public function formatMessage ($message) {
        //return parent::formatMessage($message);

        list($text, $level, $category, $timestamp) = $message;
        $level = Logger::getLevelName($level);
        if (!is_string($text)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                $text = (string) $text;
            } else {
                $text = VarDumper::export($text);
            }
        }
        $traces = [];
        if (isset($message[4])) {
            foreach ($message[4] as $trace) {
                $traces[] = "in {$trace['file']}:{$trace['line']}";
            }
        }
        $prefix = $this->getMessagePrefix($message);

        $str = "====================================================================================================\n";
        $str .= "Time: ".$this->getTime($timestamp)."\n";
        $str .= "Level: ".$level."\n";
        $str .= "Category: ".$category."\n";
        $str .= "User: ".$prefix."\n";
        $str .= "Message: ".$text."\n\n";
        $str .= (empty($traces) ? '' : "\n    " . implode("\n    ", $traces));
        $str .= "\n====================================================================================================";


        return $str;
    }
}