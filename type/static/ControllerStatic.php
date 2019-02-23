<?php

namespace Yocto;

class ControllerStatic extends Controller
{

    public function edit()
    {
        // Librairies
        $this->setVendor('https://cdn.ckeditor.com/ckeditor5/11.2.0/classic/ckeditor.js');
        $this->setVendor('https://cdn.ckeditor.com/ckeditor5/11.2.0/classic/translations/fr.js');
        // Affichage
        $this->setView('edit');
        $this->setLayout('main');
    }

    public function index()
    {
        // Affichage
        $this->setView('index');
        $this->setLayout('main');
    }

    public function save()
    {
        // Mise à jour de la page
        $pageRow = $this->_page;
        $pageRow->title = $this->get('title', true);
        // Mise à jour du type
        $typeRow = $this->_type;
        $typeRow->content = $this->get('content', true);
        // Enregistrement
        Database::saveAll([$pageRow, $typeRow]);
        // Alerte
        $this->setAlert('Modifications enregistrées.');
        // Affichage
        $this->edit();
    }

}