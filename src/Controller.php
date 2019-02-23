<?php

namespace Yocto;

class Controller
{

    /** @var Database Configuration */
    public $_configuration;

    /** @var Database Page courante */
    public $_page;

    /** @var Database Données du type de la page courante */
    public $_type;

    /** @var Database Utilisateur courant */
    public $_user;

    /** @var array Alerte */
    private $alert = [
        'text' => '',
        'type' => '',
    ];

    /** @var string Layout */
    private $layout;

    /** @var array Méthodes HTTP */
    private $methods = [];

    /** @var array Notices */
    private $notices = [];

    /** @var Template Template */
    private $template;

    /** @var array Librairies */
    private $vendors = [];

    /** @var string Vue */
    private $view;

    /**
     * Constructeur de la classe
     * @param Database $_page
     * @param Database $_user
     * @throws \Exception
     */
    public function __construct(Database $_configuration, Database $_page, Database $_type, Database $_user)
    {
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
    public function get($key, $required = false)
    {
        // Une méthode spécifique est demandée
        if (strpos($key, ':') !== false) {
            list($method, $key) = explode(':', $key);
            if (empty($this->methods[$method][$key]) === false) {
                return $this->methods[$method][$key];
            }
        } // Recherche dans les méthodes
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
            // Retourne null car cela bloque l'enregistrement des données
            return null;
        }
        return '';
    }

    /**
     * Accès au texte de l'alerte
     * @return string
     */
    public function getAlertText()
    {
        return $this->alert['text'];
    }

    /**
     * Accès au type de l'alerte
     * @return string
     */
    public function getAlertType()
    {
        return $this->alert['type'];
    }

    /**
     * Accès à une ou aux notices
     * @param null $key Clé à rechercher
     * @return array|string
     */
    public function getNotices($key = null)
    {
        if ($key) {
            return isset($this->notices[$key]) ? $this->notices[$key] : '';
        } else {
            return $this->notices;
        }
    }

    /**
     * Accès au template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Charge le layout
     */
    public function loadLayout()
    {
        require ROOT . '/layout/' . $this->layout . '/' . $this->layout . '.php';
    }

    /**
     * Charge la vue
     */
    public function loadView()
    {
        $class = strtolower(str_replace('Yocto\Controller', '', get_class($this)));
        require ROOT . '/type/' . $class . '/view/' . $this->view . '.php';
        if (is_file(ROOT . '/type/' . $class . '/view/' . $this->view . '.js')) {
            echo '<script>';
            require ROOT . '/type/' . $class . '/view/' . $this->view . '.js';
            echo '</script>';
        }
    }

    /**
     * Configure l'alerte de soumission
     * @param string|null $text Texte
     * @param string $type Type (primary, secondary, success, danger, warning, info, light, dark)
     */
    public function setAlert($text, $type = 'success')
    {
        $this->alert = [
            'text' => $text,
            'type' => $type,
        ];
    }

    /**
     * Configure un layout
     * @param string $layout Layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Configure une librairie
     * @param string $url Url de la librairie
     * @param string $sri SRI de la librairie (facultatif)
     */
    public function setVendor($url, $sri = '')
    {
        $this->vendors[$url] = $sri;
    }

    /**
     * Configure la vue
     * @param string $view Vue
     */
    public function setView($view)
    {
        $this->view = $view;
    }

}