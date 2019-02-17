<?php

namespace Yocto;

class Route {

    /**
     * PROPRIÉTÉS PRIVÉES
     */

    /** @var callable Fonction de callback */
    private $callback;

    /** @var array Liste des correspondances */
    private $matches = [];

    /** @var string Chemin de la route */
    private $path;

    /**
     * MÉTHODES PUBLIQUES
     */

    /**
     * Constructeur de la classe
     * @param string $path Chemin de la route
     * @param callable $callback Fonction de callback
     */
    public function __construct($path, $callback) {
        $this->path = trim($path, '/');
        $this->callback = $callback;
    }

    /**
     * Appel du callback
     * @return mixed
     */
    public function call() {
        return call_user_func_array($this->callback, $this->matches);
    }

    /**
     * Recherche des correspondances
     * @param string $url Url
     * @return bool
     * @throws \Exception
     */
    public function match($url) {
        // Crée une regex basée sur les types
        $regex = preg_replace_callback('/\[([a-z]+)\:([a-z]+)\]/i', function($matches) {
            switch($matches[1]) {
                case 'int':
                    return '([0-9]+)';
                case 'str':
                    return '([a-z-]+)';
                default:
                    throw new \Exception('Filter "' . $matches[1] . '" is not defined');
            }
        }, $this->path);
        // Recherche des correspondances
        if (preg_match('/^' . $regex . '$/i', $url, $matches) > 0) {
            array_shift($matches);
            $this->matches = $matches;
            return true;
        }
        else {
            return false;
        }
    }

}