<?php

namespace Yocto;

class Template {

    /**
     * PROPRIÉTÉS PRIVÉES
     */

    /** @var Controller Contrôleur en provenance de ./src/Controller.php */
    private $controller;

    /**
     * MÉTHODES PRIVÉES
     */

    /**
     * Formate les attributs
     * @param array $array Liste des attributs ($key => $value)
     * @param array $exclude Clés à ignorer ($key)
     * @return string
     */
    private function sprintAttributes(array $array = [], array $exclude = []) {
        $exclude = array_merge([
            'before',
            'label',
        ], $exclude);
        $attributes = [];
        foreach ($array as $key => $value) {
            if (in_array($key, $exclude) === false) {
                $attributes[] = sprintf('%s="%s"', $key, $value);
            }
        }
        return implode(' ', $attributes);
    }

    /**
     * MÉTHODES PUBLIQUES
     */

    /**
     * Constructeur de la classe
     * @param $controller
     */
    public function __construct($controller) {
        $this->controller = $controller;
    }

    /**
     * Crée un champ
     * @param string $nameId Nom et id de l'élément
     * @param array $attributes Attributs de l'élément ($key => $value)
     * @return string
     */
    public function input($nameId, array $attributes = []) {
        // Attributs par défaut
        $attributes = array_merge([
            'class' => '',
            'id' => $nameId,
            'maxlength' => '500',
            'name' => $nameId,
            'type' => 'text',
        ], $attributes);
        $attributes['class'] .= ' form-element__input';
        // Restore la valeur lors d'une erreur de soumission
        if ($value = $this->controller->get('POST:' . $nameId)) {
            $attributes['value'] = $value;
        }
        // Ajoute le label
        $html = '';
        if ($attributes['label']) {
            $html .= $this->label($attributes['id'], $attributes['label']);
        }
        // Ajoute la notice
        if (isset($this->controller->notices[$attributes['id']])) {
            $attributes['class'] .= ' form-element--notice';
            $html .= $this->notice($attributes['id'], $this->controller->notices[$attributes['id']]);
        }
        // Retourne l'élément
        $html .= sprintf(
            '<input %s>',
            $this->sprintAttributes($attributes)
        );
        return $html;
    }

    /**
     * Crée un label
     * @param string $for Attribut for de l'élément
     * @param string $text Texte de l'élément
     * @param array $attributes Attributs de l'élément ($key => $value)
     * @return string
     */
    public function label($for, $text, array $attributes = []) {
        // Attributs par défaut
        $attributes = array_merge([
            'class' => '',
            'for' => $for,
        ], $attributes);
        $attributes['class'] .= ' form-element__label';
        // Retourne l'élément
        return sprintf(
            '<label %s>%s</label>',
            $this->sprintAttributes($attributes),
            $text
        );
    }

    /**
     * Crée une notice
     * @param string $id Id de l'élément
     * @param string $text Texte de l'élément
     * @param array $attributes Attributs de l'élément ($key => $value)
     * @return string
     */
    public function notice($id, $text, array $attributes = []) {
        // Attributs par défaut
        $attributes = array_merge([
            'class' => '',
            'id' => $id . '__notice',
        ], $attributes);
        $attributes['class'] .= ' form-element__notice';
        // Retourne l'élément
        return sprintf(
            '<span %s>%s</span>',
            $this->sprintAttributes($attributes),
            $text
        );
    }

    /**
     * Crée un bouton
     * @param string $nameId Nom et id de l'élément
     * @param string $text Texte de l'élément
     * @param array $attributes Attributs de l'élément ($key => $value)
     * @return string
     */
    public function button($nameId, $text, array $attributes = []) {
        // Attributs par défaut
        $attributes = array_merge([
            'class' => '',
            'id' => $nameId,
            'name' => $nameId,
            'type' => 'button',
        ], $attributes);
        $attributes['class'] .= ' form-element__submit';
        // Retourne l'élément
        return sprintf(
            '<button type="submit" %s>%s</button>',
            $this->sprintAttributes($attributes),
            $text
        );
    }

}