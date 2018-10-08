<?php

define('ROOT', __DIR__);

// Session
session_start();

// Autoloader
require ROOT . '/src/Autoloader.php';
Yocto\Autoloader::register();

// Router
$router = new Yocto\Router($_GET['page']);
$router->map('GET', '/[str:pageId]', function($pageId) {
    return new Yocto\Init($pageId);
});
$router->run();