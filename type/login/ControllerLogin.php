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
        $email = $this->get('email', true);
        $password = $this->get('password', true);
        // Recherche l'utilisateur
        $user = Database::instance('user')
            ->where('email', '=', $email)
            ->find();
        // Connexion réussie
        if ($user->id AND password_verify($password, $user->password)) {
            // Création du cookie
            setcookie('YOCTO_USER_ID', $user->id, time() + 3600 * 24);
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