<?php

function appconfig($key = null) {
    static $store;

    if($store == null) {
        $store = include('appconfig.php');
    }

    $path = explode('.',$key);      // db,host
    $root = $store;
    while(count($path) > 0) {
        $key = array_shift($path);
        if(array_key_exists($key,$root)) {
            $root = $root[$key];
        }
    }
    return $root;
}

function get_route_options($from = false) {
    $opts = ($from)?$_POST:$_GET;
    foreach($opts as $name=>$val) {
        $val = mysql_real_escape_string($val);
    }
    return $opts;
}

function app_connect($db) {
    $db = appconfig($db);
    $c = mysql_connect($db['host'],$db['user'],$db['pass']);
    $s = mysql_select_db($db['name'],$c);
}

function pw_hash($val) {
    return sha1(md5('16198tg1er@#$f').$val.md5($val));
}

function appinit() {
    appconfig();
}