<?php

namespace Yocto;

class ControllerNavigationManager extends Controller
{

    /** @var array Items du menu de navigation */
    public $navigations = [];

    /** @var array Pages formatés pour le champ de sélection */
    public $pageOptions = [];

    /** @var array Types de page */
    public $types = [];

    public function index()
    {
        // Items du menu de navigation
        $this->navigations = Database::instance('navigation')
            ->orderBy('position', 'ASC')
            ->findAll();
        // Pages
        $pages = Database::instance('page')
            ->orderBy('title', 'ASC')
            ->findAll();
        foreach ($pages as $page) {
            $this->pageOptions[$page->id] = $page->title;
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
        if ($uids = $this->get('uid')) {
            $blanks = $this->get('blank');
            $ids = $this->get('id');
            $navigationUids = $this->get('navigation-uid');
            $pageIds = $this->get('page-id');
            $titles = $this->get('title');
            $visibilities = $this->get('visibility');
            // Enregistre les liens parents
            $position = 0;
            foreach ($uids as $index => $uid) {
                if ((int)$navigationUids[$index] === 0) {
                    $row = Database::instance('navigation')
                        ->where('id', '=', (int)$ids[$index])
                        ->find();
                    $row->blank = $blanks[$index];
                    $row->navigationId = 0;
                    $row->pageId = $pageIds[$index];
                    $row->position = $position++;
                    $row->title = $titles[$index];
                    $row->visibility = $visibilities[$index];
                    $row->save();
                    $uidsIds[$uid] = $ids[$index];
                }
            }
            // Enregistre les liens enfants
            $position = 0;
            foreach ($uids as $index => $uid) {
                if ((int)$navigationUids[$index]) {
                    $row = Database::instance('navigation')
                        ->where('id', '=', (int)$ids[$index])
                        ->find();
                    $row->blank = $blanks[$index];
                    $row->navigationId = $uidsIds[$navigationUids[$index]];
                    $row->pageId = $pageIds[$index];
                    $row->position = $position++;
                    $row->title = $titles[$index];
                    $row->visibility = $visibilities[$index];
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