<?php

namespace Yocto;

class ControllerStatic extends Controller {

    /**
     * MÉTHODES PUBLIQUES
     */

    public function edit() {
        $this->setVendor('ckeditor');
        $this->setView('edit');
        $this->setLayout('main');
    }

    public function index() {
        $this->setView('index');
        $this->setLayout('main');
    }

    public function save() {
        $this->setView('index');
        $this->setLayout('main');
    }

}