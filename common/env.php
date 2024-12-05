<?php
date_default_timezone_set('Europe/London');

$json = json_decode(file_get_contents(dirname(__DIR__).'/env.json'), true);
foreach ($json as $key => $val){
    defined($key) or define($key, $val);
}


/**
 * @param string $key
 * @param mixed  $default
 * @return mixed
 */
function env ($key, $default = false) {

    if(defined($key)){
        $value = constant($key);
    }

    if (!isset($value)) {
        $value = $default;
    }

    return $value;
}