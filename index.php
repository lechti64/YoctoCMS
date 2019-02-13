<?php

define('ROOT', __DIR__);

// Ouvre une session
session_start();

// Charge l'autoloader des classes
require ROOT . '/src/Autoloader.php';
Yocto\Autoloader::register();

// Crée une instance de la base de données
$db = new Yocto\Database();

// Récupère l'id de la page courante
$pageId = Yocto\Input::get('pageId');
if(empty($pageId)) {
    $pageId = $db->select('setting', 'general', 'defaultPageId');
}

// Importe le routeur du type rattaché à la page courante
$router = new Yocto\Router($pageId);
$router->map('GET', '/[str:pageId]', function() use ($db, $pageId) {
    /** @var Yocto\Controller $controller */
    $controller = require ROOT . '/type/' . $db->select('page', $pageId, 'type') . '/router.php';
    $controller->loadLayout();
});
$router->run();