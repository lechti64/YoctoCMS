<?php

namespace Yocto;

class Router
{

    /** @var Route[][] Liste des routes */
    private $routes = [
        'GET' => [],
        'POST' => [],
    ];

    /** @var string Url */
    private $url;

    /**
     * Constructeur de la classe
     * @param string $url Url
     */
    public function __construct($url)
    {
        $this->url = trim($url, '/');
    }

    /**
     * Crée une route
     * @param string $methods Méthodes HTTP, utiliser une barre verticale comme séparateur
     * @param string $path Chemin de la route
     * @param callable $callback Fonction de callback
     * @throws \Exception
     */
    public function map($methods, $path, $callback)
    {
        // Extrait les méthodes
        $methods = explode('|', $methods);
        foreach ($methods as $method) {
            // Méthode introuvable
            if (isset($this->routes[$method]) === false) {
                throw new \Exception('Method "' . $method . '" does not exist');
            }
            // Crée la route
            $route = new Route($path, $callback);
            // Ajout de la route à la propriété $this->routes
            $this->routes[$method][] = $route;
        }
    }

    /**
     * Exécute le routeur
     * @throws \Exception
     */
    public function run()
    {
        // Méthode introuvable
        if (isset($this->routes[$_SERVER['REQUEST_METHOD']]) === false) {
            throw new \Exception('Method "' . $_SERVER['REQUEST_METHOD'] . '" does not exist');
        }
        // Recherche la route dans la propriété $this->routes
        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
            if ($route->match($this->url)) {
                return $route->call();
            }
        }
        // Route introuvable
        throw new \Exception('Route not found');
    }

}