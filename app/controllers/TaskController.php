<?php

class TaskController
{

    static private $rootDir;
    static private $dirContents = array();
    static private $dirContentsSorted = array();

    // Render taskX.phtml based on get request
    public function index($request, $uriParams){
        return require __DIR__.'/../views/task'.$uriParams.'.phtml';
    }


    // Process POST data and choose requested function
    public function process($input = '', $uriParams = ''){
        switch($uriParams){
            case '1':
                $result = $this->isPalindrome($input);
                $this->redirect('/task/1', $result);
                break;
            case '2':
                $result = $this->baseToExponent($input);
                $this->redirect('/task/2', $result);
                break;
            case '3':
                $result = $this->mostCommonArrayElem($input);
                $this->redirect('/task/3', $result);
                break;
            case '4':
                $result = $this->lowestArrayNumber($input);
                $this->redirect('/task/4', $result);
                break;
            case '5':
                $result = $this->longestCommonString($input);
                $this->redirect('/task/5', $result);
                break;
            case '6':
                $result = $this->closestGroups($input);
                $this->redirect('/task/6', $result);
                break;
            case '7':
                $result = $this->locastic();
                $this->redirect('/task/7', $result);
                break;
            case '8':
                self::$rootDir = __DIR__.'/../../';
                $this->recursiveFolderScan(self::$rootDir);
                $result = array();
                $result['dirContents'] = self::$dirContents;
                $result['dirContentsSorted'] = self::$dirContentsSorted;
                $this->redirect('/task/8', $result);
                break;
            default:
                // Show 404 page (route not defined)
                $route = new Route();
                return $route->get('', 'IndexController@missing', array(), '');
        }
    }


    /**
     * Redirect to requested url and put data in $_SESSION variable
     * @param $url
     * @param $data
     */
    private function redirect($url, $data){

        $_SESSION['data'] = $data;
        header('Location: ' . $url);

    }


    /**
     * Test if given string is a palindrome
     * @param $input
     * @return bool
     */
    private function isPalindrome($input){

        $inputString = $input['palindrome'];

        /*// Way 1
        $len = strlen($inputString);
        $middle = round($len/2);
        $substr1 = substr($inputString,0,($middle-1));
        $substr2 = substr($inputString, ($middle),$len);
        return ($substr1 == strrev($substr2)) ? true : false;*/

        /*// Way 2
        $reversed = strrev($inputString);
        return ($inputString == $reversed) ? true : false;*/

        /*// Way 3
        $reversed='';
        for($t=(strlen($inputString)-1); $t>=0; $t--){
            $reversed.=substr($inputString,$t,1);
        }
        return ($inputString == $reversed) ? true : false;*/

        // Way 4
        $strToArray = str_split($inputString);
        $len = sizeof($strToArray);
        $reversed = array();
        for ($i=($len-1); $i>=0; $i--) {
            $reversed[]=$strToArray[$i];
        }
        $reversed = implode('', $reversed);
        return ($inputString == $reversed) ? true : false;

    }


    /**
     * Calculate given base number to the power of given exponent
     * @param $input
     * @return mixed
     */
    private function baseToExponent($input){

        $base = $input['number1'];
        $exponent = $input['number2'];

        // Validate inputs
        if(!is_numeric($base) || !is_numeric($exponent)) return false;

        // Calculate
        if($exponent==0){
            // Any number to the power of 0 is 1
            $temp_result = 1;
        } else {
            $temp_result = $base;
            for($i=1; $i<abs($exponent); $i++){
                $temp_result *= abs($base);
            }
            // If power number is negative
            if($exponent<0) $temp_result = 1/$temp_result;
        }
        // Pass inputs for frontend
        $result['firstNum'] = $base;
        $result['secondNum'] = $exponent;
        $result['result'] = $temp_result;

        return $result;

    }


    /**
     * Find most common array element
     * @param $input
     * @return mixed
     */
    private function mostCommonArrayElem($input){

        if(empty($input['inputArray'])) return false;
        $inputArray = explode(',', $input['inputArray']);
        $controlArray = array();

        // Take every input element
        foreach($inputArray as $element){
            $match = false;
            // Go through control array and check if current element is repeated
            foreach ($controlArray as $key => $controlArrayElem) {
                if($controlArrayElem['element']==$element){
                    // If repeated, increase 'repeats' for that element by one
                    $controlArray[$key]['repeats']++;
                    $match = true;
                    break;
                }
            }
            // If element is not in control array, place it in and set 'repeats' to 1
            if(!$match) {
                $controlArrayElemKey = count($controlArray);
                $controlArray[$controlArrayElemKey]['element'] = $element;
                $controlArray[$controlArrayElemKey]['repeats'] = 1;
            }
        }

        // Find which element in control array is most common
        $noMostCommonElem = false; // If there are two or more most common elements, this function fails
        $mostCommonElemNumber = $controlArray[0]['element'];
        $mostCommonElemRepeats = $controlArray[0]['repeats'];
        // If there is only one element in array, that element will be returned as result, for-loop will not execute
        for($i=1; $i<count($controlArray); $i++){
            // If next element in control array has greater repeats, set it as most common element
            if($controlArray[$i]['repeats']>$mostCommonElemRepeats){
                $mostCommonElemRepeats = $controlArray[$i]['repeats'];
                $mostCommonElemNumber = $controlArray[$i]['element'];
                $noMostCommonElem = false;
            } elseif($mostCommonElemRepeats==$controlArray[$i]['repeats']){
                // If next element in control array has same repeats, it is possible that there are two elements that are most common
                // In that case, this function fails
                $noMostCommonElem = true;
            }
        }

        return ($noMostCommonElem) ? false : $mostCommonElemNumber;

    }


    /**
     * Find lowest number in an array
     * @param $input
     * @return bool
     */
    private function lowestArrayNumber($input){

        if($input['inputArray']=='') return false;
        $inputArray = explode(',', $input['inputArray']);

        // First element of an array is set as lowest, then compared further with other elements
        $min = $inputArray[0];
        foreach($inputArray as $element){
            // If element is NaN
            if(!is_numeric($element)) return false;
            // If next element is lower, set it as $min
            if($element<$min) $min = $element;
        }

        return $min;

    }


    /**
     * Find longest common string in two given strings
     * @param $input
     * @return bool
     */
    private function longestCommonString($input){

        if($input['string1']=='' || $input['string2']=='') return false;
        if($input['string1']==$input['string2']) return $input['string1'];
        $firstString = str_split($input['string1']);
        $secondString = str_split($input['string2']);
        $longestStringsArray = array();

        // String elements must not be numbers
        foreach ($firstString as $firstStringElem) if(is_numeric($firstStringElem)) return false;
        foreach ($secondString as $secondStringElem) if(is_numeric($secondStringElem)) return false;

        // Take every letter from first string and try to find same letter in second string
        $firstStringLastKey = array_pop(array_keys($firstString));
        $secondStringLastKey = array_pop(array_keys($secondString));
        foreach($firstString as $firstStringKey => $firstStringLetter){
            foreach ($secondString as $secondStringKey => $secondStringLetter) {
                $longest = array();
                $firstStringKeyToCompare = $firstStringKey;
                $secondStringKeyToCompare = $secondStringKey;
                // If match found, save that letter and increment first and second key in array and compare them
                while($firstString[$firstStringKeyToCompare]==$secondString[$secondStringKeyToCompare]){
                    $longest[] = $firstString[$firstStringKeyToCompare];
                    // If one of the arrays came to an end
                    if($firstStringKeyToCompare==$firstStringLastKey || $secondStringKeyToCompare==$secondStringLastKey) break;
                    $firstStringKeyToCompare++;
                    $secondStringKeyToCompare++;
                }
                // Save found occurrences
                $longestStringsArray[] = implode('', $longest);
            }
        }

        // Find longest word in $longestStringsArray
        $longestWord = $longestStringsArray[0];
        foreach ($longestStringsArray as $longestStringsArrayElem) {
            if(strlen($longestStringsArrayElem)>strlen($longestWord)) $longestWord = $longestStringsArrayElem;
        }

        return (!empty($longestWord)) ? $longestWord : false;

    }


    /**
     * Divide array into closest possible sum groups
     * @param $input
     * @return array|bool
     */
    private function closestGroups($input){

        if(empty($input['inputArray']) || empty($input['inputGroups'])) return false;
        $inputArray = explode(',', $input['inputArray']);
        $inputGroups = $input['inputGroups'];
        $outputArray = array();

        // Check if values are numeric
        foreach($inputArray as $inputArrayElem) if(!is_numeric($inputArrayElem)) return false;
        if(!is_numeric($inputGroups)) return false;
        // Check that number of groups is not greater than number of elements in an input array
        if($inputGroups>count($inputArray)) return false;

        // Create desired number of groups
        for($i=0; $i<$inputGroups; $i++) $outputArray[] = array();
        // Sort input array descending
        rsort($inputArray);
        // Take each element from input array and go through groups
        foreach ($inputArray as $inputArrayElem) {
            // Find group with lowest sum
            $lowestSum = array_sum($outputArray[0]);
            $lowestSumKey = 0;
            foreach($outputArray as $key => $outputArrayElem){
                $outputArrayElemSum = array_sum($outputArrayElem);
                if($outputArrayElemSum<$lowestSum){
                    $lowestSum = $outputArrayElemSum;
                    $lowestSumKey = $key;
                }
            }
            // Add input element to group with lowest sum
            $outputArray[$lowestSumKey][] = $inputArrayElem;
        }

        // Create output array
        $result = array();
        foreach ($outputArray as $outputArrayElem) {
            // Show all elements in group and their sum
            $outputArrayElemImploded = implode(',', $outputArrayElem);
            $outputArrayElemFinal = $outputArrayElemImploded . ' = ' . array_sum($outputArrayElem);
            $result['result'][] = $outputArrayElemFinal;
        }

//        var_dump($outputArray);
        $result['inputArray'] = $input['inputArray'];
        $result['inputGroups'] = $input['inputGroups'];
        return $result;

    }


    /**
     * Find numbers divisible by 3 or 5
     * @return array
     */
    private function locastic(){

        $outputArray = array();

        for($i=1; $i<=100; $i++){
            $three = false;
            $five = false;

            /*// Way 1
            // Division will give 'integer' or 'double' result
            // Only numbers divisible by required numbers will give integers
            if(gettype($i/3)=='integer') $three = true;
            if(gettype($i/5)=='integer') $five = true;*/

            // Way 2
            // If divisible by required numbers, result will not have any floating decimals
            // so the modulus remainder will be zero
            // Notice: this function will catch only first digit after decimal point
            if( ( ($i/3)*10 )%10 === 0 ) $three = true;
            if( ( ($i/5)*10 )%10 === 0 ) $five = true;

            if($three && !$five) $outputArray[] = 'LOCA';
            if(!$three && $five) $outputArray[] = 'STIC';
            if($three && $five) $outputArray[] = 'LOCASTIC';
            if(!$three && !$five) $outputArray[] = $i;
        }

        return $outputArray;

    }


    /**
     * Recursive scan function
     * @param $dirToScan
     * @return array
     */
    static private function recursiveFolderScan($dirToScan){

        $outputArray = array();
        $dir = scandir($dirToScan);

        // Take every dir element
        foreach($dir as $dirElem){
            // Remember key for creating multidimensional array
            $key = count(self::$dirContents);
            if($dirElem==='.' || $dirElem==='..' || $dirElem==='.git' || $dirElem==='.idea') continue;

            // Remove root dir path
            $path = $dirToScan.DIRECTORY_SEPARATOR.$dirElem;
            $path = substr($path, strlen(self::$rootDir)+1);

            // If file found, save it and iterate further
            if(is_file($dirToScan.DIRECTORY_SEPARATOR.$dirElem)){
                self::$dirContents[$key]['path'] = $path;
                self::$dirContents[$key]['type'] = 'file';
                $outputArray[] = $dirToScan.DIRECTORY_SEPARATOR.$dirElem;
                continue;
            }

            // If not file, then it is directory, save it
            self::$dirContents[$key]['path'] = $path;
            self::$dirContents[$key]['type'] = 'folder';
            // Recursive call with subdirectory as a parameter, result is whole subdir structure
            // This function calls itself until dir without subdirs is found, then returns it's structure,
            // closes that recursive call and does the same thing until all recursive calls are closed
            foreach (self::recursiveFolderScan($dirToScan.DIRECTORY_SEPARATOR.$dirElem) as $value) {
                $outputArray[] = $value;
            }
        }

        return $outputArray;

    }

}

?>