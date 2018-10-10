<?php

namespace Yocto;

class ControllerStatic extends Controller {

    /**
     * PUBLIC METHODS
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
        $this->setLayout('raw');
    }

}