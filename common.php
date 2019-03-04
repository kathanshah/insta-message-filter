<?php

// Function used to print data for debugging purpose
function pre($data, $isContinue=0){
    echo "<pre>";
    print_r($data);
    echo "</pre>";

    if(!$isContinue)
        die();
}

// Function used to get class methods
function prea($obj, $isContinue=0){
    pre(get_class_methods($obj), $isContinue);
}

function printDebugMessage($message) {
    echo PHP_EOL.PHP_EOL.$message.PHP_EOL.PHP_EOL;
}

function placeholders($text, $count=0, $separator=","){
    $result = array();
    if($count > 0){
        for($x=0; $x<$count; $x++){
            $result[] = $text;
        }
    }

    return implode($separator, $result);
}

function getSleepTime() {
    $array = [6,7,8,9];
    $k = array_rand($array);
    return $array[$k];
}
