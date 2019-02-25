<?php

namespace Yocto;

class ControllerNavigationManager extends Controller
{

    /** @var array Liens enfants */
    public $navigationLinksChildren = [];

    /** @var array Liens parents */
    public $navigationLinksParents = [];

    /** @var array Pages */
    public $pages = [];

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
        // Affichage
        $this->setView('index');
        $this->setLayout('main');
    }

    public function save()
    {
        $uidsIds = [];
        if ($ids = $this->get('id')) {
            $blanks = $this->get('blank');
            $navigationLinkUids = $this->get('navigation-link-uid');
            $pageIds = $this->get('page-id');
            $titles = $this->get('title');
            $visibilities = $this->get('visibility');
            // Enregistre les liens parents
            $position = 0;
            foreach ($ids as $uid => $id) {
                if ((int)$navigationLinkUids[$uid] === 0) {
                    $row = Database::instance('navigation-link')
                        ->where('id', '=', (int)$ids[$uid])
                        ->find();
                    $row->blank = $blanks[$uid];
                    $row->navigationLinkId = 0;
                    $row->pageId = $pageIds[$uid];
                    $row->position = $position++;
                    $row->title = $titles[$uid];
                    $row->visibility = $visibilities[$uid];
                    $row->save();
                    $uidsIds[$uid] = $row->id;
                }
            }
            // Enregistre les liens enfants
            // TODO: Ce système de position n'est pas top
            $position = 0;
            foreach ($ids as $uid => $id) {
                if ((int)$navigationLinkUids[$uid]) {
                    $row = Database::instance('navigation-link')
                        ->where('id', '=', (int)$ids[$uid])
                        ->find();
                    $row->blank = $blanks[$uid];
                    $row->navigationLinkId = $uidsIds[$navigationLinkUids[$uid]];
                    $row->pageId = $pageIds[$uid];
                    $row->position = $position;
                    $row->title = $titles[$uid];
                    $row->visibility = $visibilities[$uid];
                    $row->save();
                }
            }
            // Alerte
            $this->setAlert('Modifications enregistrées.');
        } else {
            // Alerte
            $this->setAlert('La navigation doit comporter un lien au minimum.', 'danger');
        }

        // Affichage
        $this->index();
    }

}