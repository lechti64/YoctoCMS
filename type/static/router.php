<?php

$router = new Yocto\Router($_GET['action']);
$router->map('GET', '/', function() {
    $controller = new Yocto\ControllerStatic();
    $controller->index();
    return $controller;
});
$router->map('GET', '/edit', function() {
    $controller = new Yocto\ControllerStatic();
    $controller->edit();
    return $controller;
});
$router->map('POST', '/edit', function() {
    $controller = new Yocto\ControllerStatic();
    $controller->save();
    return $controller;
});
return $router->run();