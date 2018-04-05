<?php

namespace Yocto;

class Router {

    /**
     * PRIVATE PROPERTIES
     */

    /** @var Route[][] List of routes */
    private $routes = [
        'GET' => [],
        'POST' => []
    ];

    /** @var string Url */
    private $url;

    /**
     * PUBLIC METHODS
     */

    /**
     * Router constructor
     * @param string $url Url
     */
    public function __construct($url) {
        $this->url = trim($url, '/');
    }

    /**
     * Route mapping
     * @param string $method HTTP method
     * @param string $path Routing path
     * @param callable $callback Callback function
     * @throws \Exception
     */
    public function map($method, $path, $callback) {
        // Method does not exist
        if(isset($this->routes[$method]) === false) {
            throw new \Exception('Method "' . $method . '" does not exist');
        }
        // Create route
        $route = new Route($path, $callback);
        // Add route in routes array
        $this->routes[$method][] = $route;
    }

    /**
     * Run routing
     * @throws \Exception
     */
    public function run() {
        // Method does not exist
        if(isset($this->routes[$_SERVER['REQUEST_METHOD']]) === false) {
            throw new \Exception('Method "' . $_SERVER['REQUEST_METHOD'] . '" does not exist');
        }
        // Search route in routes array
        foreach($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
            if($route->match($this->url)) {
                return $route->call();
            }
        }
        // Route not found
        throw new \Exception('Route not found');
    }

}