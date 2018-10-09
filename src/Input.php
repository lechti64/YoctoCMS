<?php

namespace Yocto;

class Input {

    /**
     * PUBLIC METHODS
     */

    /**
     * @param $key
     * @return string
     */
    public static function get($key) {
        if(isset($_POST[$key])) {
            return $_POST[$key];
        }
        elseif(isset($_GET[$key])) {
            return $_GET[$key];
        }
        else {
            return "";
        }
    }

}