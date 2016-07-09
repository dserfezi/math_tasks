<?php

class App
{

    public static function run()
    {
        session_save_path(__DIR__.'/sessions');
        session_start();
        App::processUri();
    }


    public static function processUri(){

        require_once __DIR__.'/controllers/RouteController.php';

        // Check if this route exists
        $routes = require __DIR__ . '/routes.php';
        foreach($routes as $key => $value){
            // Take URI and remove ending slash if there is one
            $uri = $_SERVER['REQUEST_URI'];
            if(substr($uri, -1) == '/') $uri = substr($uri, 0, strlen($uri)-1);
            // Take parameters if any
            if(strpos($key, '{') !== false){
                $key = explode('{', $key);
                $key = $key[0];
                // Remove ending slash if there is one
                if(substr($key, -1) == '/') $key = substr($key, 0, strlen($key)-1);
                // Remove parameter from uri so it can be compared
                $uri = explode('/', $uri);
                // If asset requested
                if($uri[1]=='assets'){
                    $uriParams = array();
                    for($i=2; $i<count($uri); $i++){
                        $uriParams[] = $uri[$i];
                    }
                    $uri = '/' . $uri[1];
                    $uriParams = implode('/', $uriParams);
                } else {
                    $uriParams = array_pop($uri);
                    $uri = implode('/', $uri);
                }
            }
            // If route is defined
            if($uri == $key || $uri == ''){
                $noRoute = false;
                // Take uri parameters if any
                $uriParams = (isset($uriParams)) ? $uriParams : '';
                // Send to proper Route function to create view
                $route = new Route();
                if ($_SERVER["REQUEST_METHOD"] == "POST"){
                    return $route->post($value['post'], $_POST, $uriParams);
                }
                if ($_SERVER["REQUEST_METHOD"] == "GET"){
                    return $route->get($uri, $value['get'], $_GET, $uriParams);
                }
            } else {
                $noRoute = true;
            }
        }
        if(isset($noRoute) && $noRoute){
            // Show 404 page (route not defined)
            $route = new Route();
            return $route->get('', 'IndexController@missing', array(), '');
        }

    }

}