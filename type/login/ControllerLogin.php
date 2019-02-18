<?php

namespace Yocto;

class ControllerLogin extends Controller {

    /**
     * MÉTHODES PUBLIQUES
     */

    public static function _initDatabase() {
        if (Database::exists('page-login') === false) {
            Database::create('page-login', []);
        }
    }

    public function index() {
        $this->setView('index');
        $this->setLayout('main');
    }

    public function login() {
        // Test required
        $this->get('id', true);
        $this->get('password', true);
        $this->setView('index');
        $this->setLayout('main');
    }

}