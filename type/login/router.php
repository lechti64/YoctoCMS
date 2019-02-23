<?php

$controller = new Yocto\ControllerLogin($_configuration, $_page, $_type, $_user);
$router = new Yocto\Router($controller->get('action'));
$router->map('GET', '/', function () use ($controller) {
    $controller->index();
    return $controller;
});
$router->map('POST', '/', function () use ($controller) {
    $controller->login();
    return $controller;
});
return $router->run();