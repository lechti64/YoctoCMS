<?php

namespace Yocto;

class ControllerStatic extends Controller {

    /**
     * MÉTHODES PUBLIQUES
     */

    public function edit() {
        $this->setVendor('https://cdn.ckeditor.com/ckeditor5/11.2.0/classic/ckeditor.js');
        $this->setVendor('https://cdn.ckeditor.com/ckeditor5/11.2.0/classic/translations/fr.js');
        $this->setView('edit');
        $this->setLayout('main');
    }

    public function index() {
        $this->setView('index');
        $this->setLayout('main');
    }

    public function save() {
        $row = $this->_page;
        $row->title = $this->get('title', true);
        $row->save();
        $row = $this->_type;
        $row->content = $this->get('content', true);
        $row->save();
        $this->setVendor('https://cdn.ckeditor.com/ckeditor5/11.2.0/classic/ckeditor.js');
        $this->setVendor('https://cdn.ckeditor.com/ckeditor5/11.2.0/classic/translations/fr.js');
        $this->setView('edit');
        $this->setLayout('main');
    }

}