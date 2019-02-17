<?php

define('ROOT', __DIR__);

// Ouvre une session
session_start();

// Charge l'autoloader des classes
require ROOT . '/src/Autoloader.php';
Yocto\Autoloader::register();

// Récupère l'utilisateur courant
$_userId = (isset($_COOKIE['userId'])
    ? $_COOKIE['userId']
    : ''
);
$_user = Yocto\Database::instance('user')->where('id', '=', $_userId)->find();

// Récupère la page courante
$_pageId = (empty($_GET['pageId'])
    ? Yocto\Database::instance('setting')->where('id', '=', 'general')->find()->defaultPageId
    : $_GET['pageId']
);
$_page = Yocto\Database::instance('page')->where('id', '=', $_pageId)->find();

// Importe le routeur du type de la page courante
$router = new Yocto\Router($_page->id);
$router->map('GET|POST', '/[str:pageId]', function() use ($_page, $_user) {
    /** @var Yocto\Controller $controller */
    $controller = require ROOT . '/type/' . $_page->type . '/router.php';
    $controller->loadLayout();
});
$router->run();