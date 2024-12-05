<?php

if(!empty($css)){
    \admin\assets\MailAsset::register($this);
}

echo $content;