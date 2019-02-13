<?php

namespace Yocto;

class Controller {

    /**
     * PROPRIÉTÉS PRIVÉES
     */

    /** @var Database */
    private $db;

    /** @var string */
    private $layout;

    /** @var string */
    private $pageId;

    /** @var Template */
    private $template;

    /** @var array */
    private $vendors = [
        'ckeditor' => false
    ];

    /** @var string */
    private $view;

    /**
     * MÉTHODES PUBLIQUES
     */

    /**
     * Constructeur de la classe
     * @param $db Database
     * @param $pageId string
     */
    public function __construct($db, $pageId) {
        // Transmet au contrôleur de la page l'instance de la base de données et l'id de la page courante en provenance de ./index.php
        $this->db = $db;
        $this->pageId = $pageId;
        // Crée une instance du template
        $this->template = new Template();
    }

    /**
     * Charge le layout
     */
    public function loadLayout() {
        require ROOT . '/layout/' . $this->layout . '/' . $this->layout . '.php';
    }

    /**
     * Charge la vue
     */
    public function loadView() {
        $class = strtolower(str_replace('Yocto\Controller', '', get_class($this)));
        require ROOT . '/type/' . $class . '/view/' . $this->view . '.php';
    }

    /**
     * @param $layout
     */
    public function setLayout($layout) {
        $this->layout = $layout;
    }

    /**
     * @param $vendor
     */
    public function setVendor($vendor) {
        $this->vendors[$vendor] = true;
    }

    /**
     * @param $view
     */
    public function setView($view) {
        $this->view = $view;
    }

}