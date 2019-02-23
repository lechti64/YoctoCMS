<?php

$controller = new Yocto\ControllerStatic($_configuration, $_page, $_type, $_user);
$router = new Yocto\Router($controller->get('action'));
$router->map('GET', '/', function () use ($controller) {
    $controller->index();
    return $controller;
});
$router->map('GET', '/edit', function () use ($controller) {
    $controller->edit();
    return $controller;
});
$router->map('POST', '/edit', function () use ($controller) {
    $controller->save();
    return $controller;
});
return $router->run();