<?php

return array(
    '/' => array(
        'post' => '',
        'get' => 'IndexController@index'
    ),
    '/task/{id}' => array(
        'post' => 'TaskController@process',
        'get' => 'TaskController@index'
    ),
    '/assets/{asset}' => array(
        'post' => '',
        'get' => 'IndexController@assets'
    )
);

?>