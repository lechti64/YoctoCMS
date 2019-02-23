<?php

$controller = new Yocto\ControllerNavigationManager($_configuration, $_page, $_type);
$router = new Yocto\Router($controller->get('action'));
$router->map('GET', '/', function () use ($controller) {
    $controller->index();
    return $controller;
});
$router->map('POST', '/', function () use ($controller) {
    $controller->save();
    return $controller;
});
return $router->run();