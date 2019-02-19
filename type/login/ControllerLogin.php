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
        $this->get('id', true);
        $this->get('password', true);
        $this->setAlert('Connexion réussie');
        $this->index();
    }

}