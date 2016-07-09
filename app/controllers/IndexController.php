<?php

class IndexController
{

    /**
     * Handle root page
     * @param $request
     */
    public function index($request){

        return require __DIR__.'/../views/main.phtml';

    }


    /**
     * Handle asset request
     * @param $request
     * @param $uriParams
     * @return mixed
     */
    public function assets($request, $uriParams){

        return require __DIR__.'/../../public/'.$uriParams;

    }


    /**
     * Handle 404 error page
     */
    public function missing(){

        return require __DIR__.'/../views/errors/404.phtml';

    }
}

?>