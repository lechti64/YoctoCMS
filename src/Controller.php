<?php

namespace Yocto;

class Controller {

    /**
     * PROPRIÉTÉS PUBLIQUES
     */

    /** @var \stdClass Page courante */
    public $_page;

    /** @var \stdClass Utilisateur courant */
    public $_user;

    /** @var array Notices */
    public $notices = [];

    /**
     * PROPRIÉTÉS PRIVÉES
     */

    /** @var string Layout */
    private $layout;

    /** @var array Méthodes HTTP */
    private $methods = [];

    /** @var Template Template */
    private $template;

    /** @var array Librairies */
    private $vendors = [
        'ckeditor' => false
    ];

    /** @var string Vue */
    private $view;

    /**
     * MÉTHODES PUBLIQUES
     */

    /**
     * Constructeur de la classe
     * @param \stdClass $_page
     * @param \stdClass $_user
     * @throws \Exception
     */
    public function __construct($_page, $_user) {
        $this->_page = $_page;
        $this->_user = $_user;
        $this->template = new Template($this);
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
        if (strpos($key, ':') !== false) {
            list($method, $key) = explode(':', $key);
            if (empty($this->methods[$method][$key]) === false) {
                return $this->methods[$method][$key];
            }
        }
        // Recherche dans les méthodes
        else {
            foreach ($this->methods as $method) {
                if (empty($method[$key]) === false) {
                    return $method[$key];
                }
            }
        }
        // Génère une notice
        if ($required) {
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
     * Configure un layout
     * @param $layout
     */
    public function setLayout($layout) {
        $this->layout = $layout;
    }

    /**
     * Configure les librairies
     * @param $vendor
     */
    public function setVendor($vendor) {
        $this->vendors[$vendor] = true;
    }

    /**
     * Configure la vue
     * @param $view
     */
    public function setView($view) {
        $this->view = $view;
    }

}