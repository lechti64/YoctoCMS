<?php

define('ROOT', __DIR__);

// Session
session_start();

// Autoloader
require ROOT . '/src/Autoloader.php';
Yocto\Autoloader::register();

// Database
$db = new Yocto\Database();

// Default page id
$pageId = Yocto\Input::get('pageId');
if(empty($pageId)) {
    $pageId = $db->select('setting', 'general', 'defaultPageId');
}

// Router
$router = new Yocto\Router($pageId);
$router->map('GET', '/[str:pageId]', function($pageId) use ($db) {
    return new Yocto\Init($pageId, $db);
});
$router->run();