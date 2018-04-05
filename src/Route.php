<?php

namespace Yocto;

class Route {

    /**
     * PRIVATE PROPERTIES
     */

    /** @var callable Callback function */
    private $callback;

    /** @var array List of matches */
    private $matches = [];

    /** @var string Routing path */
    private $path;

    /**
     * PUBLIC METHODS
     */

    /**
     * Route constructor
     * @param string $path Routing path
     * @param callable $callback Callback function
     */
    public function __construct($path, $callback) {
        $this->path = trim($path, '/');
        $this->callback = $callback;
    }

    /**
     * Call callback
     * @return mixed
     */
    public function call() {
        return call_user_func_array($this->callback, $this->matches);
    }

    /**
     * Route matching
     * @param string $url Url
     * @return bool
     * @throws \Exception
     */
    public function match($url) {
        // Creating regex based on types
        $regex = preg_replace_callback('/\[([a-z]+)\:([a-z]+)\]/i', [$this, 'matchCallback'], $this->path);
        // Search values in url
        if(preg_match('/^' . $regex . '$/i', $url, $matches) > 0) {
            array_shift($matches);
            $this->matches = $matches;
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * PRIVATE METHODS
     */

    /**
     * Route matching callback
     * @param $matches
     * @return string
     * @throws \Exception
     */
    private function matchCallback($matches) {
        switch($matches[1]) {
            case 'int':
                return '([0-9]+)';
            case 'str':
                return '([a-z-]+)';
            default:
                throw new \Exception('Filter "' . $matches[1] . '" is not defined');
        }
    }

}