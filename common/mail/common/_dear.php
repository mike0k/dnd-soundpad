<?php
if(empty($name)){
    $name = '';
}

if(!empty($user) && !empty($user->name)){
    $name = $user->name;
}
?>

<p>Hi <?= $name ?>,</p>