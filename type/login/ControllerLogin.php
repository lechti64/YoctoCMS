<?php

namespace Yocto;

class ControllerLogin extends Controller {

    /**
     * MÉTHODES PUBLIQUES
     */

    public function index() {
        $this->setView('index');
        $this->setLayout('main');
    }

    public function login() {
        // Test required
        $this->get('email', true);
        $this->get('password', true);
        $this->setView('index');
        $this->setLayout('main');
    }

}