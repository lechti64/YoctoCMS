<?php

namespace Yocto;

class Session
{

    const VISIBILITY_INHERIT = 'inherit';
    const VISIBILITY_PRIVATE = 'private';
    const VISIBILITY_PUBLIC = 'public';
    const VISIBILITY_PUBLIC_ONLY = 'public-only';

    /** @var Controller Contrôleur en provenance de ./src/Controller.php */
    private $controller;

    /**
     * Constructeur de la classe
     * @param $controller
     */
    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Test si l'utilisateur est connecté
     * @return bool
     */
    public function isLoggedIn()
    {
        return (empty($this->controller->get('COOKIE:YOCTO_LOGGEDIN')) === false);
    }

    /**
     * Test si un objet est visible pour l'utilisateur
     * @param \stdClass $object Objet
     * @return bool
     * @throws \Exception
     */
    public function isVisible($object)
    {
        if ($object->visibility === self::VISIBILITY_INHERIT) {
            $pageRow = Database::instance('page')
                ->where('id', '=', $object->pageId)
                ->find();
            $visibility = $pageRow->id
                ? $pageRow->visibility
                : self::VISIBILITY_PUBLIC;
        } else {
            $visibility = $object->visibility;
        }
        switch ($visibility) {
            case self::VISIBILITY_PRIVATE:
                return $this->isLoggedIn();
            case self::VISIBILITY_PUBLIC:
                return true;
            case self::VISIBILITY_PUBLIC_ONLY:
                return ($this->isLoggedIn() === false);
            default:
                throw new \Exception('"' . $visibility . '" visibility not found');
        }
    }

}