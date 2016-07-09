<?php
class Route
{

    public function get($request, $controllerAndFunction, $data, $uriParams = ''){

        $ctrlAndFunc = explode('@', $controllerAndFunction);
        $controller = $ctrlAndFunc[0];
        $function = $ctrlAndFunc[1];

        include __DIR__.'/'.$controller.'.php';
        // Get view via controller
        $executeCtrlFunc = new $controller;
        // If asset required
        if($request=='/assets'){
            header("Content-type: text/css");
            return $executeCtrlFunc->$function($request, $uriParams);
        } else {
            ob_start();
            $executeCtrlFunc->$function($request, $uriParams);
            $view = ob_get_contents();
            ob_end_clean();
        }




        // Split view into pieces
        $view = $this->chopView($view);

        // Concatenate layout and requested view pieces
        return $this->render($view);

    }


    public function post($controllerAndFunction, $data, $uriParams){

        $ctrlAndFunc = explode('@', $controllerAndFunction);
        $controller = $ctrlAndFunc[0];
        $function = $ctrlAndFunc[1];

        include __DIR__.'/'.$controller.'.php';
        // Get view via controller
        $executeCtrlFunc = new $controller;
        ob_start();
        $executeCtrlFunc->$function($data, $uriParams);
        $view = ob_get_contents();
        ob_end_clean();
        // Split view into pieces
        $view = $this->chopView($view);

        // Concatenate layout and requested view pieces
        return $this->render($view);

    }


    /**
     * Cut $view string into pieces based on @extend and @section key words
     * @param string $view
     * @return array $result
     */
    private function chopView($view){

        $result = array();
        $chops = explode('@extends->', $view);
        $chops = explode('@section->', $chops[1]);
        $result['extends'] = trim($chops[0]);

        for($i=1; $i<count($chops); $i++){
            $segment = '';
            // Piece of string inside section area including section name
            $segment = $chops[$i];
            // Name of the section
            $sectionName = strtok($segment, PHP_EOL);
            // Piece of string inside section area
            $section = explode($sectionName, $segment);
            $section = $section[1];

            // Save in result
            $result[$sectionName] = $section;
        }

        return $result;

    }


    /**
     * Include parts of the evaluated view
     * Parts are separated by @section
     * @param $part
     * @param $view
     * @return string
     */
    private function includeViewParts($part, $view){
        if(isset($view[$part])){
            return $view[$part];
        } else {
            return '';
        }

    }


    /**
     * Render layout and requested view
     * @param $view
     * @return mixed
     */
    private function render($view){

        // TODO: define behaviour if @extend not defined in view
        return require __DIR__.'/../views/layouts/'.$view['extends'].'.phtml';

    }

}
?>