<?php

namespace Yocto;

class Controller {

    /**
     * PROPRIÉTÉS PUBLIQUES
     */

    /** @var Database */
    public $db;

    /** @var array */
    public $notices = [];

    /**
     * PROPRIÉTÉS PRIVÉES
     */

    /** @var string */
    private $layout;

    /** @var array */
    private $methods = [];

    /** @var string */
    private $pageId;

    /** @var Template */
    private $template;

    /** @var int */
    private $userId;

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
    public function __construct($db, $pageId, $userId) {
        // Transmet au contrôleur de la page les données suivantes en provenance de ./index.php :
        // - l'instance de la base de données
        $this->db = $db;
        // - l'id de la page courante
        $this->pageId = $pageId;
        // - l'id de l'utilisateur courant
        $this->userId = $userId;
        // Crée une instance du template
        $this->template = new Template($this);
        // Liste des méthodes
        $this->methods = [
            'POST' => $_POST,
            'GET' => $_GET,
            'COOKIE' => $_COOKIE,
        ];
    }

    /**
     * Recherche une clé dans les méthodes HTTP
     * @param string $key Clé à rechercher
     * @param bool $required Clé obligatoire, sinon génère une notice
     * @return string
     */
    public function get($key, $required = false) {
        // Une méthode spécifique est demandée
        if(strpos($key, ':') !== false) {
            list($method, $key) = explode(':', $key);
            if(empty($this->methods[$method][$key]) === false) {
                return $this->methods[$method][$key];
            }
        }
        // Recherche dans les méthodes
        else {
            foreach($this->methods as $method) {
                if(empty($method[$key]) === false) {
                    return $method[$key];
                }
            }
        }
        // Génère une notice
        if($required) {
            $this->notices[$key] = 'Obligatoire';
        }
        // Clé introuvable
        return "";
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