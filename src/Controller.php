<?php

namespace Yocto;

abstract class Controller implements ControllerInterface {

    /**
     * PROPRIÉTÉS PUBLIQUES
     */

    /** @var Database Configuration */
    public $_configuration;

    /** @var Database Page courante */
    public $_page;

    /** @var Database Données du type de la page courante */
    public $_type;

    /** @var Database Utilisateur courant */
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
    private $vendors = [];

    /** @var string Vue */
    private $view;

    /**
     * MÉTHODES PUBLIQUES
     */

    /**
     * Constructeur de la classe
     * @param Database $_page
     * @param Database $_user
     * @throws \Exception
     */
    public function __construct(Database $_configuration, Database $_page, Database $_type, Database $_user) {
        // Transmet les données en provenance de ./index.php
        $this->_configuration = $_configuration;
        $this->_page = $_page;
        $this->_type = $_type;
        $this->_user = $_user;
        // Crée l'instance du template
        $this->template = new Template($this);
        // Ajoute les méthodes HTTP
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
            $this->notices[$key] = 'Champ requis';
        }
        // Clé introuvable
        return "";
    }

    /**
     * Accès au template
     */
    public function getTemplate() {
        return $this->template;
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
        if (is_file(ROOT . '/type/' . $class . '/view/' . $this->view . '.js.php')) {
            echo '<script>';
            require ROOT . '/type/' . $class . '/view/' . $this->view . '.js.php';
            echo '</script>';
        }
    }

    /**
     * Configure un layout
     * @param string $layout Layout
     */
    public function setLayout($layout) {
        $this->layout = $layout;
    }

    /**
     * Configure une librairie
     * @param string $url Url de la librairie
     * @param string $sri SRI de la librairie (facultatif)
     */
    public function setVendor($url, $sri = '') {
        $this->vendors[$url] = $sri;
    }

    /**
     * Configure la vue
     * @param string $view Vue
     */
    public function setView($view) {
        $this->view = $view;
    }

}