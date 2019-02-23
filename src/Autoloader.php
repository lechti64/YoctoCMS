<?php

namespace Yocto;

class Autoloader
{

    /**
     * Enregistre la fonction d'autoload
     */
    public static function register()
    {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    /**
     * Fonction d'autoload
     * @param string $class Classe
     */
    public static function autoload($class)
    {
        // Supprime le namespace
        $class = str_replace('Yocto\\', '', $class);
        // Importe le fichier de la classe
        if (is_file(ROOT . '/src/' . $class . '.php')) {
            require ROOT . '/src/' . $class . '.php';
        } else {
            $directoryName = strtolower(str_replace('Controller', '', $class));
            if (is_file(ROOT . '/type/' . $directoryName . '/' . $class . '.php')) {
                require ROOT . '/type/' . $directoryName . '/' . $class . '.php';
            }
        }
    }

}