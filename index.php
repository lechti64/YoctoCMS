<?php

define('ROOT', __DIR__);

// Ouvre une session
session_start();

// Charge l'autoloader des classes
require ROOT . '/src/Autoloader.php';
Yocto\Autoloader::register();

// Crée une instance de la base de données
$db = new Yocto\Database();

// Récupère l'id de l'utilisateur courant
$userId = isset($_COOKIE['userId']) ? $_COOKIE['userId'] : '';

// Récupère l'id de la page courante
if(empty($_GET['pageId'])) {
    $pageId = $db->select('setting', 'general', 'defaultPageId');
}
else {
    $pageId = $_GET['pageId'];
}

// Importe le routeur du type rattaché à la page courante
$router = new Yocto\Router($pageId);
$router->map('GET', '/[str:pageId]', function() use ($db, $pageId, $userId) {
    /** @var Yocto\Controller $controller */
    $controller = require ROOT . '/type/' . $db->select('page', $pageId, 'type') . '/router.php';
    $controller->loadLayout();
});
$router->map('POST', '/[str:pageId]', function() use ($db, $pageId, $userId) {
    /** @var Yocto\Controller $controller */
    $controller = require ROOT . '/type/' . $db->select('page', $pageId, 'type') . '/router.php';
    $controller->loadLayout();
});
$router->run();