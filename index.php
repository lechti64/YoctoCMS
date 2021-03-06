<?php

define('ROOT', __DIR__);

// Ouvre une session
session_start();

// Charge les autoloaders
require ROOT . '/vendor/autoload.php';
require ROOT . '/src/Autoloader.php';
Yocto\Autoloader::register();

// Charge le gestionnaire d'erreurs
$whoops = new Whoops\Run;
$whoops->pushHandler(new Whoops\Handler\PrettyPageHandler);
$whoops->register();
function dump($expression)
{
    ob_start();
    var_dump($expression);
    echo '<pre>' . ob_get_clean() . '</pre>';
}

// Génère la base de données par défaut
$checkConfiguration = Yocto\Database::instance('configuration')
    ->where('id', '=', 1)
    ->find();
if ($checkConfiguration->id === 0) {
    $defaultData = json_decode(file_get_contents(Yocto\Database::PATH . '/default.json'), true);
    foreach ($defaultData as $table => $rows) {
        foreach ($rows as $index => $columns) {
            $row = Yocto\Database::instance($table);
            foreach ($columns as $columnId => $columnValue) {
                $row->{$columnId} = $columnValue;
                $row->save();
            }
        }
    }
}

// Récupère la configuration
$_configuration = Yocto\Database::instance('configuration')
    ->where('id', '=', 1)
    ->find();

// Récupère la page courante
$_pageId = (empty($_GET['pageId'])
    ? $_configuration->defaultPageId
    : (int)$_GET['pageId']
);
$_page = Yocto\Database::instance('page')
    ->where('id', '=', $_pageId)
    ->find();

// Récupère les données du type rattaché à la page courante
$_type = Yocto\Database::instance('page-' . $_page->type)
    ->where('pageId', '=', $_page->id)
    ->find();

// Importe le routeur du type de la page courante
$router = new Yocto\Router($_page->id);
$router->map('GET|POST', '/[int:pageId]', function () use ($_configuration, $_page, $_type) {
    /** @var Yocto\Controller $controller */
    $controller = require ROOT . '/type/' . $_page->type . '/router.php';
    $controller->loadLayout();
});
$router->run();