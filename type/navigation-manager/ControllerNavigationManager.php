<?php

namespace Yocto;

class ControllerNavigationManager extends Controller
{

    /** @var array Liens enfants */
    public $navigationLinksChildren = [];

    /** @var array Liens parents */
    public $navigationLinksParents = [];

    /** @var array Pages */
    public $pages = [
        0 => 'Aucune'
    ];

    /** @var array Types de page */
    public $types = [];

    public function index()
    {
        // Liens enfants
        $this->navigationLinksChildren = Database::instance('navigation-link')
            ->where('navigationLinkId', '!=', 0)
            ->orderBy('position', 'ASC')
            ->findAll();
        // Liens enfants
        $this->navigationLinksParents = Database::instance('navigation-link')
            ->where('navigationLinkId', '=', 0)
            ->orderBy('position', 'ASC')
            ->findAll();
        // Pages
        $pages = Database::instance('page')
            ->orderBy('title', 'ASC')
            ->findAll();
        foreach ($pages as $page) {
            $this->pages[$page->id] = $page->title;
        }
        // Types de page
        $this->types = array_diff(scandir(ROOT . '/type'), array('.', '..'));
        // Librairies
        $this->setVendor('https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.8.3/Sortable.min.js');
        $this->setVendor(
            'https://cdnjs.cloudflare.com/ajax/libs/fontawesome-iconpicker/3.2.0/css/fontawesome-iconpicker.min.css',
            'sha256-yExEWA6b/bqs3FXxQy03aOWIFtx9QEVnHZ/EwemRLbc='
        );
        $this->setVendor(
            'https://cdnjs.cloudflare.com/ajax/libs/fontawesome-iconpicker/3.2.0/js/fontawesome-iconpicker.min.js',
            'sha256-QvBM3HnWsvcCWqpqHZrtoefjB8t2qPNzdSDNyaSNH5Q='
        );
        // Affichage
        $this->setView('index');
        $this->setLayout('main');
    }

    public function save()
    {
        $uidsIds = [];
        if ($ids = $this->get('id')) {
            // Liens avant enregistrement
            $navigationLinksOld = Database::instance('navigation-link')->findAll();
            // Extrait les données
            $blanks = $this->get('blank');
            $icons = $this->get('icon');
            $navigationLinkUids = $this->get('navigation-link-uid');
            $pageIds = $this->get('page-id');
            $titles = $this->get('title');
            $visibilities = $this->get('visibility');
            // Enregistre les liens parents
            $position = 0;
            $positions = [];
            foreach ($ids as $uid => $id) {
                if ((int)$navigationLinkUids[$uid] === 0) {
                    $row = Database::instance('navigation-link')
                        ->where('id', '=', (int)$ids[$uid])
                        ->find();
                    $row->blank = $blanks[$uid];
                    $row->icon = $icons[$uid];
                    $row->navigationLinkId = 0;
                    $row->pageId = $pageIds[$uid];
                    $row->position = $position++;
                    $row->title = $titles[$uid];
                    $row->visibility = $visibilities[$uid];
                    $row->save();
                    $uidsIds[$uid] = $row->id;
                    $positions[$uid] = 0;
                }
            }
            // Enregistre les liens enfants
            foreach ($ids as $uid => $id) {
                if ((int)$navigationLinkUids[$uid]) {
                    $row = Database::instance('navigation-link')
                        ->where('id', '=', (int)$ids[$uid])
                        ->find();
                    $row->blank = $blanks[$uid];
                    $row->icon = $icons[$uid];
                    $row->navigationLinkId = $uidsIds[$navigationLinkUids[$uid]];
                    $row->pageId = $pageIds[$uid];
                    $row->position = $positions[$navigationLinkUids[$uid]]++;
                    $row->title = $titles[$uid];
                    $row->visibility = $visibilities[$uid];
                    $row->save();
                    $uidsIds[$uid] = $row->id;
                }
            }
            // Supprime les liens
            foreach ($navigationLinksOld as $navigationLink) {
                if (in_array($navigationLink->id, $uidsIds) === false) {
                    Database::instance('navigation-link')
                        ->where("id", "=", $navigationLink->id)
                        ->find()
                        ->delete();
                }
            }
            // Alerte
            $this->setAlert('Modifications enregistrées.');
        } else {
            // Alerte
            $this->setAlert('Le menu doit comporter un lien au minimum.', 'danger');
        }

        // Affichage
        $this->index();
    }

}