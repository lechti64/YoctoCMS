<?php

namespace Yocto;

class Init {

    /**
     * PRIVATE PROPERTIES
     */

    private $db;

    private $pageId;

    /**
     * PUBLIC METHODS
     */

    /**
     * Init constructor.
     * @param $pageId
     * @throws \Exception
     */
    public function __construct($pageId) {
        // Database
        $this->db = new Database();
        // Current page id
        $this->pageId = $pageId;
        // Router
        /** @var Controller $controller */
        $controller = require ROOT . '/type/' . $this->db->select('page', $this->pageId, 'type') . '/router.php';
        // Layout
        $controller->loadLayout();
    }

}