<?php

namespace Yocto;

class Database implements \IteratorAggregate, \Countable {

    /**
     * PROPRIÉTÉS PRIVÉES
     */

    /** @var array Conditions applicables lors d'une requête */
    private $conditions = [
        'limit'   => [],
        'orderBy' => [],
        'where'   => [],
    ];

    /** @var array Lignes en sortie */
    private $outputRows = [];

    /** @var array Lignes de la table */
    private $tableRows = [];

    /**
     * METHODES PRIVÉES
     */

    /**
     * Applique les conditions
     * @return array
     * @throws \Exception
     */
    private function applyConditions() {
        $outputRows = $this->outputRows;
        if ($this->conditions['where']) {
            $outputRows = $this->applyWhere($outputRows);
        }
        if ($this->conditions['orderBy']) {
            $this->applyOrderBy($outputRows);
        }
        if ($this->conditions['limit']) {;
            $outputRows = $this->applyLimit($outputRows);
        }
        return array_values($outputRows);
    }

    /**
     * Applique la condition limit
     * @param array $outputRows Lignes en sortie à traiter
     * @return array
     */
    private function applyLimit($outputRows) {
        return array_slice(
            $outputRows,
            $this->conditions['limit']['offset'],
            $this->conditions['limit']['length']
        );
    }

    /**
     * Applique la condition orderBy
     * @param array $outputRows Lignes en sortie à traiter
     */
    private function applyOrderBy(&$outputRows) {
        uasort($outputRows, function($a, $b) {
            if ($this->conditions['orderBy']['direction'] === 'ASC') {
                return strnatcasecmp(
                    $a->{$this->conditions['orderBy']['column']},
                    $b->{$this->conditions['orderBy']['column']}
                );
            }
            else if ($this->conditions['orderBy']['direction'] === 'DESC') {
                return strnatcasecmp(
                    $b->{$this->conditions['orderBy']['column']},
                    $a->{$this->conditions['orderBy']['column']}
                );
            }
            else {
                throw new \Exception('Direction "'. $this->conditions['orderBy'] . '" unknown');
            }
        });
    }

    /**
     * Applique la condition where
     * @param array $outputRows Lignes en sortie à traiter
     * @return array
     */
    private function applyWhere($outputRows) {
        return array_filter($outputRows, function($row) {
            $isFound = [
                'AND' => null,
                'OR' => null,
            ];
            foreach ($this->conditions['where'] as $condition) {
                // Stop la recherche si une condition OR = true
                if ($isFound['OR']) {
                    break;
                }
                // Ignore les conditions AND lorsqu'une condition AND = false
                if ($condition['logicalOperator'] === 'AND' AND $isFound['AND'] === false) {
                    continue;
                }
                // Check le contenu des colonnes
                switch($condition['comparisonOperators']) {
                    case '=':
                        $isFound[$condition['logicalOperator']] = ($row->{$condition['column']} === $condition['value']);
                        break;
                    case '!=':
                        $isFound[$condition['logicalOperator']] = ($row->{$condition['column']} !== $condition['value']);
                        break;
                    case '>':
                        $isFound[$condition['logicalOperator']] = ($row->{$condition['column']} > $condition['value']);
                        break;
                    case '>=':
                        $isFound[$condition['logicalOperator']] = ($row->{$condition['column']} >= $condition['value']);
                        break;
                    case '<':
                        $isFound[$condition['logicalOperator']] = ($row->{$condition['column']} < $condition['value']);
                        break;
                    case '<=':
                        $isFound[$condition['logicalOperator']] = ($row->{$condition['column']} <= $condition['value']);
                        break;
                    case 'IN':
                        $isFound[$condition['logicalOperator']] = in_array($row->{$condition['column']}, $condition['value']);
                        break;
                    case 'NOT IN':
                        $isFound[$condition['logicalOperator']] = (in_array($row->{$condition['column']}, $condition['value']) === false);
                        break;
                    case 'LIKE':
                        $isFound[$condition['logicalOperator']] = preg_match(
                            '/^' . str_replace('%', '(.*?)', preg_quote($condition['value'])) . '$/i',
                            $row->{$condition['column']}
                        );
                        break;
                    default:
                        throw new \Exception('Logical operator "'. $condition['logicalOperator'] . '" unknown');
                }
            }
            return ($isFound['OR'] OR $isFound['AND']);
        });
    }

    /**
     * MÉTHODES PUBLIQUES
     */

    /**
     * Retourne la valeur d'une colonne lorsque la méthode find est utilisée
     * @param string $column Nom de la colonne
     * @return mixed
     */
    public function __get($column) {
        return $this->outputRows->$column;
    }

    /**
     * Alias de la méthode where
     * @param string $column Nom de la colonne
     * @param string $comparisonOperators Opérateur de comparaison (=, !=, >, >=, <, <=, IN, NOT IN, LIKE)
     * @param string $value Valeur de la colonne
     * @return $this
     */
    public function andWhere($column, $comparisonOperators, $value) {
        $this->where($column, $comparisonOperators, $value);
        return $this;
    }

    /**
     * Compte le nombre de lignes en sortie
     * @return int
     */
    public function count() {
        return count($this->outputRows);
    }

    /**
     * Supprime une ligne
     */
    public function delete() {}

    /**
     * Retourne une ligne
     * @return $this
     * @throws \Exception
     */
    public function find() {
        $this->outputRows = $this->applyConditions();
        if ($this->count()) {
            $this->outputRows = $this->outputRows[0];
        }
        return $this;
    }

    /**
     * Retourne toutes les lignes
     * @return $this
     * @throws \Exception
     */
    public function findAll() {
        $this->outputRows = $this->applyConditions();
        return $this;
    }

    /**
     * Crée l'itérateur à partir de la propriété $this->>outputRows
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new \ArrayIterator($this->outputRows);
    }

    /**
     * Crée une instance de la table
     * @param string $table Nom de la table
     * @return Database
     * @throws \Exception
     */
    public static function instance($table) {
        if (is_dir(ROOT . '/content/data/' . $table) === false) {
            throw new \Exception('Table "'. $table . '" not found');
        }
        $self = new self();
        foreach (new \DirectoryIterator(ROOT . '/content/data/' . $table) as $file) {
            if ($file->getExtension() === 'json') {
                $row = json_decode(file_get_contents(ROOT . '/content/data/' . $table . '/' . $file->getFilename()));
                $row->id = $file->getBasename('.json');
                $self->tableRows[] = $row;
            }
        }
        $self->outputRows = $self->tableRows;
        return $self;
    }

    /**
     * Extrait une portion des lignes
     * @param int $offset Index de début
     * @param int $length Nombre de lignes à extraire
     * @return $this
     */
    public function limit($offset, $length) {
        $this->conditions['limit'] = [
            'length' => $length,
            'offset' => $offset,
        ];
        return $this;
    }

    /**
     * Tri les lignes
     * @param string $column Colonne à utiliser pour le tri
     * @param string $direction Ordre de tri (ASC ou DESC)
     * @return $this
     */
    public function orderBy($column, $direction = 'ASC') {
        $this->conditions['orderBy'] = [
            'column' => $column,
            'direction' => $direction,
        ];
        return $this;
    }

    /**
     * Alias de la méthode where
     * @param string $column Nom de la colonne
     * @param string $comparisonOperators Opérateur de comparaison (=, !=, >, >=, <, <=, IN, NOT IN, LIKE)
     * @param string $value Valeur de la colonne
     * @return $this
     */
    public function orWhere($column, $comparisonOperators, $value) {
        $this->where($column, $comparisonOperators, $value, 'OR');
        return $this;
    }

    /**
     * Enregistre la table
     */
    public function save() {}
    
    /**
     * Ajout d'une condition sur une colonne
     * @param string $column Nom de la colonne
     * @param string $comparisonOperators Opérateur de comparaison (=, !=, >, >=, <, <=, IN, NOT IN, LIKE)
     * @param string $value Valeur de la colonne
     * @param string $logicalOperator Opérateur logique (AND ou OR)
     * @return $this
     */
    public function where($column, $comparisonOperators, $value, $logicalOperator = 'AND') {
        $this->conditions['where'][] = [
            'column' => $column,
            'comparisonOperators' => $comparisonOperators,
            'logicalOperator' => $logicalOperator,
            'value' => $value,
        ];
        return $this;
    }

}