<?php

namespace Yocto;

class ControllerStatic extends Controller {

    /**
     * MÉTHODES PUBLIQUES
     */

    public static function _initDatabase() {
        if (Database::exists('page-static') === false) {
            Database::create('page-static', [
                'content' => 'string',
            ]);
        }
    }

    public function edit() {
        $this->setVendor(
            'https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.0.0/tinymce.min.js',
            'sha256-3DxUi/cwxPOy+wrPilztlynbmi7v25eHEdIJh+nKFOs='
        );
        $this->setView('edit');
        $this->setLayout('main');
    }

    public function index() {
        $this->setView('index');
        $this->setLayout('main');
    }

    public function save() {
        $this->setView('index');
        $this->setLayout('main');
    }

}