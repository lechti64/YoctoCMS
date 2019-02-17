<?php

// Crée une instance du contrôleur
$controller = new Yocto\ControllerStatic($db, $pageId, $userId);

// Initialise les contrôleurs en fonction des routes
$router = new Yocto\Router($controller->get('action'));
$router->map('GET', '/', function() use ($controller) {
    $controller->index();
    return $controller;
});
$router->map('GET', '/edit', function() use ($controller) {
    $controller->edit();
    return $controller;
});
$router->map('POST', '/edit', function() use ($controller) {
    $controller->save();
    return $controller;
});
return $router->run();