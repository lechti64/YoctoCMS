<?php

namespace Yocto;

class Controller {

    /**
     * PRIVATE PROPERTIES
     */

    private $db;

    private $layout;

    private $pageId;

    private $view;

    /**
     * PUBLIC METHODS
     */

    /**
     * Controller constructor.
     * @param $db
     * @param $pageId
     */
    public function __construct($db, $pageId) {
        // Database
        $this->db = $db;
        // Current page id
        $this->pageId = $pageId;
    }

    public function getLayout() {
        return $this->layout;
    }

    public function getView() {
        return $this->view;
    }

    public function loadLayout() {
        require ROOT . '/layout/' . $this->getLayout() . '/' . $this->getLayout() . '.php';
    }

    public function loadView() {
        $class = strtolower(str_replace('Yocto\Controller', '', get_class($this)));
        require ROOT . '/type/' . $class . '/view/' . $this->getView() . '.php';
    }

    /**
     * @param $layout
     */
    public function setLayout($layout) {
        $this->layout = $layout;
    }

    /**
     * @param $view
     */
    public function setView($view) {
        $this->view = $view;
    }

}