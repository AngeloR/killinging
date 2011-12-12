<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

return array(
    'db' => array(
        'dev' => array(
            'host' => 'localhost',
            'user' => 'root',
            'pass' => '',
            'name' => 'killinging'
        ),
        'staging' => array(
            'host' => 'localhost',
            'user' => 'root',
            'pass' => '',
            'name' => 'killinging'
        ),
        'production' => array(
            'host' => 'localhost',
            'user' => '',
            'pass' => '',
            'name' => 'feethave_rpg'
        ),
    ),
    'session' => 'killinging',
    'use_db' => 'db.dev',
    'theme' => 'default',
    'view_path' => 'views/themes',
);
?>
