<?php

namespace Yocto;

class Autoloader {

    /**
     * PUBLIC METHODS
     */

    /**
     * Autoload registration
     */
    public static function register() {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    /**
     * Included the class file
     * @param string $class Class name
     */
    public static function autoload($class) {
        // Delete the namespace
        $class = str_replace(__NAMESPACE__ . '\\', '', $class);
        // Included the class file
        // Yocto class
        if(is_file(ROOT . '/src/' . $class . '.php')) {
            require ROOT . '/src/' . $class . '.php';
        }
        // Controller class
        else {
            $directoryName = strtolower(str_replace('Controller', '', $class));
            if(is_file(ROOT . '/type/' . $directoryName . '/' . $class . '.php')) {
                require ROOT . '/type/' . $directoryName . '/' . $class . '.php';
            }
        }
    }

}