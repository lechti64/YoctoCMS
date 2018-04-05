<?php

namespace Yocto;

class Controller {

    /**
     * PRIVATE PROPERTIES
     */

    private $layout;

    private $view;

    /**
     * PUBLIC METHODS
     */

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