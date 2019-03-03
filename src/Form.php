<?php

namespace Yocto;

class Form
{

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
     * Crée une case à cocher multiple
     * @param string $nameId Nom et id de l'élément
     * @param string $label Label de l'élément
     * @param array $attributes Attributs de l'élément ($key => $value)
     * @return string
     */
    public function checkbox($nameId, $label, array $attributes = [])
    {
        // Attributs par défaut
        $attributes = array_merge([
            'class' => '',
            'id' => $nameId,
            'name' => $nameId,
        ], $attributes);
        $attributes['class'] .= ' custom-control-input';
        // Restore la valeur lors d'une erreur de soumission
        if ($value = $this->controller->get('POST:' . $nameId)) {
            $attributes['value'] = $value;
        }
        // Ajoute le label
        $label = $this->label($attributes['id'], $label, [
            'class' => 'custom-control-label'
        ]);
        // Ajoute la notice
        if ($this->controller->getNotices($attributes['id'])) {
            $attributes['class'] .= ' is-invalid';
            $notice = $this->notice($attributes['id'], $this->controller->getNotices($attributes['id']));
        } else {
            $notice = '';
        }
        // Retourne l'élément
        return sprintf(
            '<div class="custom-control custom-checkbox"><input type="checkbox" %s>%s%s</div>',
            $this->sprintAttributes($attributes),
            $label,
            $notice
        );
    }

    /**
     * Crée un champ court
     * @param string $nameId Nom et id de l'élément
     * @param array $attributes Attributs de l'élément ($key => $value)
     * @return string
     */
    public function input($nameId, array $attributes = [])
    {
        // Attributs par défaut
        $attributes = array_merge([
            'class' => '',
            'id' => $nameId,
            'maxlength' => '500',
            'name' => $nameId,
            'type' => 'text',
        ], $attributes);
        $attributes['class'] .= ' form-control';
        // Restore la valeur lors d'une erreur de soumission
        if ($value = $this->controller->get('POST:' . $nameId)) {
            $attributes['value'] = $value;
        }
        // Ajoute la notice
        if ($this->controller->getNotices($attributes['id'])) {
            $attributes['class'] .= ' is-invalid';
            $notice = $this->notice($attributes['id'], $this->controller->getNotices($attributes['id']));
        } else {
            $notice = '';
        }
        // Retourne l'élément
        return sprintf(
            '<input %s>%s',
            $this->sprintAttributes($attributes),
            $notice
        );
    }

    /**
     * Crée un label
     * @param string $for Attribut for de l'élément
     * @param string $text Texte de l'élément
     * @param array $attributes Attributs de l'élément ($key => $value)
     * @return string
     */
    public function label($for, $text, array $attributes = [])
    {
        // Retourne l'élément
        return sprintf(
            '<label %s%s>%s</label>',
            $this->sprintAttributes([
                'for' => $for
            ]),
            $this->sprintAttributes($attributes),
            $text
        );
    }

    /**
     * Crée une case à cocher unique
     * @param string $nameId Nom et id de l'élément
     * @param string $label Label de l'élément
     * @param array $attributes Attributs de l'élément ($key => $value)
     * @return string
     */
    public function radio($nameId, $label, array $attributes = [])
    {
        // Attributs par défaut
        $attributes = array_merge([
            'class' => '',
            'id' => $nameId,
            'name' => $nameId,
        ], $attributes);
        $attributes['class'] .= ' custom-control-input';
        // Restore la valeur lors d'une erreur de soumission
        if ($value = $this->controller->get('POST:' . $nameId)) {
            $attributes['value'] = $value;
        }
        // Ajoute le label
        $label = $this->label($attributes['id'], $label, [
            'class' => 'custom-control-label'
        ]);
        // Ajoute la notice
        if ($this->controller->getNotices($attributes['id'])) {
            $attributes['class'] .= ' is-invalid';
            $notice = $this->notice($attributes['id'], $this->controller->getNotices($attributes['id']));
        } else {
            $notice = '';
        }
        // Retourne l'élément
        return sprintf(
            '<div class="custom-control custom-radio"><input type="radio" %s>%s%s</div>',
            $this->sprintAttributes($attributes),
            $label,
            $notice
        );
    }

    /**
     * Crée un champ de sélection
     * @param string $nameId Nom et id de l'élément
     * @param array $options Valeurs de l'élément ($key => $value)
     * @param array $attributes Attributs de l'élément ($key => $value)
     * @return string
     */
    public function select($nameId, array $options = [], array $attributes = [])
    {
        // Attributs par défaut
        $attributes = array_merge([
            'class' => '',
            'id' => $nameId,
            'name' => $nameId,
            'selected' => '',
        ], $attributes);
        $attributes['class'] .= ' custom-select';
        // Restore la valeur lors d'une erreur de soumission
        if ($value = $this->controller->get('POST:' . $nameId)) {
            $attributes['value'] = $value;
        }
        // Ajoute la notice
        if ($this->controller->getNotices($attributes['id'])) {
            $attributes['class'] .= ' is-invalid';
            $notice = $this->notice($attributes['id'], $this->controller->getNotices($attributes['id']));
        } else {
            $notice = '';
        }
        // Valeurs
        foreach ($options as $key => &$option) {
            $option = '<option value="' . $key . '"' . ($attributes['selected'] === $key ? ' selected' : '') . '>' . $option . '</option>';
        }
        unset($option);
        // Retourne l'élément
        return sprintf(
            '<select %s>%s</select>%s',
            $this->sprintAttributes($attributes),
            implode('', $options),
            $notice
        );
    }

    /**
     * Crée un champ long
     * @param string $nameId Nom et id de l'élément
     * @param string $text Texte de l'élément
     * @param array $attributes Attributs de l'élément ($key => $value)
     * @return string
     */
    public function textarea($nameId, $text, array $attributes = [])
    {
        // Attributs par défaut
        $attributes = array_merge([
            'class' => '',
            'id' => $nameId,
            'maxlength' => '5000',
            'name' => $nameId,
        ], $attributes);
        $attributes['class'] .= ' form-control';
        // Restore la valeur lors d'une erreur de soumission
        if ($value = $this->controller->get('POST:' . $nameId)) {
            $attributes['value'] = $value;
        }
        // Ajoute la notice
        if ($this->controller->getNotices($attributes['id'])) {
            $attributes['class'] .= ' is-invalid';
            $notice = $this->notice($attributes['id'], $this->controller->getNotices($attributes['id']));
        } else {
            $notice = '';
        }
        // Retourne l'élément
        return sprintf(
            '<textarea %s>%s</textarea>%s',
            $this->sprintAttributes($attributes),
            $text,
            $notice
        );
    }

    /**
     * Crée une notice
     * @param string $id Id de l'élément
     * @param string $text Texte de l'élément
     * @param array $attributes Attributs de l'élément ($key => $value)
     * @return string
     */
    private function notice($id, $text, array $attributes = [])
    {
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
    private function sprintAttributes(array $array = [], array $exclude = [])
    {
        $attributes = [];
        foreach ($array as $key => $value) {
            if (in_array($key, $exclude) === false) {
                $attributes[] = sprintf('%s="%s"', $key, $value);
            }
        }
        return implode(' ', $attributes);
    }

}