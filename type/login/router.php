<?php

// Crée une instance du contrôleur
$controller = new Yocto\ControllerLogin($db, $pageId, $userId);

// Initialise les contrôleurs en fonction des routes
$router = new Yocto\Router($controller->get('action'));
$router->map('GET', '/', function() use ($controller) {
    $controller->index();
    return $controller;
});
$router->map('POST', '/', function() use ($controller) {
    $controller->login();
    return $controller;
});
return $router->run();