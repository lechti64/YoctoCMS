<?php

namespace Yocto;

class Init {

    /**
     * PUBLIC METHODS
     */

    /**
     * Controller constructor.
     * @param $pageId
     * @throws \Exception
     */
    public function __construct($pageId) {
        $db = new Database();
        $pageType = $db->select('page', $pageId, 'type');
        // Router
        /** @var Controller $controller */
        $controller = require ROOT . '/type/' . $pageType . '/router.php';
        // Layout
        $controller->loadLayout();
    }

}