<?php

namespace Yocto;

class ControllerLogin extends Controller
{

    public function index()
    {
        // Affichage
        $this->setView('index');
        $this->setLayout('main');
    }

    public function login()
    {
        $username = $this->get('username', true);
        $password = $this->get('password', true);
        // Connexion réussie
        if (
            $this->_configuration->username === $username
            AND password_verify($password, $this->_configuration->password)
        ) {
            // Création du cookie
            setcookie('YOCTO_LOGGEDIN', true, time() + 3600 * 24);
            // Redirection sur la page d'accueil
            header('Location: ./');
            exit;
        } // Échec de connexion
        else {
            // Alerte
            $this->setAlert('Identifiant ou mot de passe incorrect.', 'danger');
            // Affichage
            $this->index();
        }
    }

}