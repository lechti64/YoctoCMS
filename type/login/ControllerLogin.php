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
        $email = $this->get('email', true);
        $password = $this->get('password', true);
        // Recherche l'utilisateur
        $user = Database::instance('user')
            ->where('email', '=', $email)
            ->find();
        // Connexion réussie
        if($user->id AND password_verify($password, $user->password)) {
            setcookie('YOCTO_USER_ID', $user->id, time() + 3600 * 24);
            header('Location: ./');
            exit;
        }
        // Échec de connexion
        else {
            $this->setAlert('Identifiant ou mot de passe incorrect.', 'danger');
            $this->index();
        }
    }

}