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
     * Crée un groupe
     * @param string $element Élément à inclure au groupe
     * @param array $attributes Attributs de l'élément ($key => $value)
     * @return string
     */
    private function formGroup($element, array $attributes = []) {
        // Attributs par défaut
        $attributes = array_merge([
            'class' => '',
        ], $attributes);
        $attributes['class'] .= ' form-group';
        // Retourne l'élément
        return sprintf(
            '<div %s>%s</div>',
            $this->sprintAttributes($attributes),
            $element
        );
    }

    /**
     * Crée un label
     * @param string $for Attribut for de l'élément
     * @param string $text Texte de l'élément
     * @param array $attributes Attributs de l'élément ($key => $value)
     * @return string
     */
    private function label($for, $text, array $attributes = []) {
        // Attributs par défaut
        $attributes = array_merge([
            'class' => '',
            'for' => $for,
        ], $attributes);
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
    private function notice($id, $text, array $attributes = []) {
        // Attributs par défaut
        $attributes = array_merge([
            'class' => '',
            'id' => $id . '__notice',
        ], $attributes);
        $attributes['class'] .= ' invalid-feedback';
        // Retourne l'élément
        return sprintf(
            '<div %s>%s</div>',
            $this->sprintAttributes($attributes),
            $text
        );
    }

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
     * Crée un bouton
     * @param string $nameId Nom et id de l'élément
     * @param string $text Texte de l'élément
     * @param array $attributes Attributs de l'élément ($key => $value)
     * @return string
     */
    public function button($nameId, $text, array $attributes = []) {
        // Attributs par défaut
        $attributes = array_merge([
            'class' => 'btn-primary',
            'id' => $nameId,
            'name' => $nameId,
            'type' => 'button',
        ], $attributes);
        $attributes['class'] .= ' btn';
        // Retourne l'élément
        return sprintf(
            '<button type="submit" %s>%s</button>',
            $this->sprintAttributes($attributes),
            $text
        );
    }

    /**
     * Crée un champ court
     * @param string $nameId Nom et id de l'élément
     * @param array $attributes Attributs de l'élément ($key => $value)
     * @return string
     */
    public function input($nameId, array $attributes = []) {
        // Attributs par défaut
        $attributes = array_merge([
            'class' => '',
            'id' => $nameId,
            'label' => '',
            'maxlength' => '500',
            'name' => $nameId,
            'type' => 'text',
        ], $attributes);
        $attributes['class'] .= ' form-control';
        if (isset($this->controller->notices[$attributes['id']])) {
            $attributes['class'] .= ' is-invalid';
        }
        // Restore la valeur lors d'une erreur de soumission
        if ($value = $this->controller->get('POST:' . $nameId)) {
            $attributes['value'] = $value;
        }
        // Ajoute le label
        $html = '';
        if ($attributes['label']) {
            $html .= $this->label($attributes['id'], $attributes['label']);
        }
        // Ajoute l'élément
        $html .= sprintf(
            '<input %s>',
            $this->sprintAttributes($attributes)
        );
        // Ajoute la notice
        if (isset($this->controller->notices[$attributes['id']])) {
            $html .= $this->notice($attributes['id'], $this->controller->notices[$attributes['id']]);
        }
        return $this->formGroup($html);
    }

    /**
     * Crée un champ long
     * @param string $nameId Nom et id de l'élément
     * @param string $text Texte de l'élément
     * @param array $attributes Attributs de l'élément ($key => $value)
     * @return string
     */
    public function textarea($nameId, $text, array $attributes = []) {
        // Attributs par défaut
        $attributes = array_merge([
            'class' => '',
            'id' => $nameId,
            'label' => '',
            'maxlength' => '5000',
            'name' => $nameId,
        ], $attributes);
        $attributes['class'] .= ' form-control';
        if (isset($this->controller->notices[$attributes['id']])) {
            $attributes['class'] .= ' is-invalid';
        }
        // Restore la valeur lors d'une erreur de soumission
        if ($value = $this->controller->get('POST:' . $nameId)) {
            $attributes['value'] = $value;
        }
        // Ajoute le label
        $html = '';
        if ($attributes['label']) {
            $html .= $this->label($attributes['id'], $attributes['label']);
        }
        // Ajoute l'élément
        $html .= sprintf(
            '<textarea %s>%s</textarea>',
            $this->sprintAttributes($attributes),
            $text
        );
        // Ajoute la notice
        if (isset($this->controller->notices[$attributes['id']])) {
            $html .= $this->notice($attributes['id'], $this->controller->notices[$attributes['id']]);
        }
        return $this->formGroup($html);
    }

}