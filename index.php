<?php

define('ROOT', __DIR__);

// Ouvre une session
session_start();

// Charge l'autoloader des classes
require ROOT . '/src/Autoloader.php';
Yocto\Autoloader::register();

// Génère la base de données par défaut
$database = json_decode(file_get_contents(Yocto\Database::PATH . '_init.json'), true);
foreach($database as $table => $rows) {
    Yocto\Database::create($table, $rows['_init']);
    foreach($rows as $rowId => $columns) {
        if($rowId !== '_init') {
            $row = Yocto\Database::instance($table);
            $row->id = $rowId;
            foreach($columns as $columnId => $columnValue) {
                $row->{$columnId} = $columnValue;
            }
            $row->save();
        }
    }
}

// Récupère la configuration
$_configuration = Yocto\Database::instance('configuration')
    ->where('id', '=', 'configuration')
    ->find();

// Récupère l'utilisateur courant
$_userId = (isset($_COOKIE['userId'])
    ? $_COOKIE['userId']
    : ''
);
$_user = Yocto\Database::instance('user')
    ->where('id', '=', $_userId)
    ->find();

// Récupère la page courante
$_pageId = (empty($_GET['pageId'])
    ? $_configuration->defaultPageId
    : $_GET['pageId']
);
$_page = Yocto\Database::instance('page')
    ->where('id', '=', $_pageId)
    ->find();

// Récupère les données du type rattaché à la page courante
$_type = Yocto\Database::instance('page-' . $_page->type)
    ->where('id', '=', $_page->id)
    ->find();

// Importe le routeur du type de la page courante
$router = new Yocto\Router($_page->id);
$router->map('GET|POST', '/[str:pageId]', function() use ($_configuration, $_page, $_type, $_user) {
    /** @var Yocto\Controller $controller */
    $controller = require ROOT . '/type/' . $_page->type . '/router.php';
    $controller->loadLayout();
});
$router->run();