<?php

namespace Yocto;

class Database implements \IteratorAggregate, \Countable {

    /**
     * CONSTANTES
     */
    
    const PATH = ROOT . '/content/data';
    
    /**
     * PROPRIÉTÉS PRIVÉES
     */

    /** @var array Conditions */
    private $conditions = [
        'limit'   => [],
        'orderBy' => [],
        'where'   => [],
    ];

    /** @var array Configuration de la table */
    private $configuration;

    /** @var \stdClass Ligne en sortie (méthode find(), insertion et mise à jour de ligne) */
    private $outputRow;

    /** @var array Lignes en sortie */
    private $outputRows = [];

    /** @var string Nom de la table */
    private $table;

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
        $outputRows = $this->tableRows;
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
     * Filtre une valeur
     * @param mixed $value Valeur
     * @param string $type Type
     * @return bool|float|int|string
     */
    private function filter($value, $type) {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'float':
                return (float) $value;
            case 'integer':
                return (int) $value;
                break;
            default:
                return (string) $value;
        }
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
        return $this->outputRow->$column;
    }

    /**
     * Insert la valeur d'une colonne
     * @param string $column Nom de la colonne
     * @param mixed $value Valeur de la colonne
     * @throws \Exception
     */
    public function __set($column, $value) {
        if (array_key_exists($column, $this->configuration['columns'])) {
            $this->outputRow->{$column} = $this->filter($value, $this->configuration['columns'][$column]);
        }
        else {
            throw new \Exception('Column "' . $column . '" not found in the table "' . $this->table . '"');
        }
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
     * Crée une table
     * @param string $table Nom de la table
     * @param array $columns Colonne et type de données
     * @throws \Exception
     */
    public static function create($table, array $columns = []) {
        if (self::exists($table) === false AND mkdir(self::PATH . '/' . $table) === false) {
            throw new \Exception('Table "' . $table . '" was not created');
        }
        if (file_put_contents(self::PATH . '/' . $table . '/conf.json', json_encode($columns, JSON_PRETTY_PRINT)) === false) {
            throw new \Exception('Unable to create the configuration file of the "' . $table . '" table');
        }
    }

    /**
     * Supprime la table, une ligne ou des lignes
     * @return bool
     * @throws \Exception
     */
    public function delete() {
        // Supprime une ligne
        if ($this->outputRow->id) {
            if (
                is_file(self::PATH . '/' . $this->table . '/' . $this->outputRow->id . '.json')
                AND unlink(self::PATH . '/' . $this->table . '/' . $this->outputRow->id . '.json') === false
            ) {
                throw new \Exception('Row "' . $this->outputRow->id . '" has not been deleted');
            }
        }
        // Supprime des lignes
        else if ($this->outputRows) {
            foreach ($this->outputRows as $row) {
                if (
                    is_file(self::PATH . '/' . $this->table . '/' . $row->id . '.json')
                    AND unlink(self::PATH . '/' . $this->table . '/' . $row->id . '.json') === false
                ) {
                    throw new \Exception('Row "' . $row->id . '" has not been deleted');
                }
            }
        }
        // Supprime la table
        else {
            if (in_array(false, array_map('unlink', glob(self::PATH . '/' . $this->table . '/*.json')))) {
                throw new \Exception('Rows have not been deleted in the table "' . $this->table . '"');
            }
            if (rmdir(self::PATH . '/' . $this->table) === false) {
                throw new \Exception('Table "' . $this->table . '" has not been deleted');
            }
        }
        return true;
    }

    /**
     * Check qu'une table existe
     * @param string $table Nom de la table
     * @return bool
     */
    public static function exists($table) {
        return is_dir(self::PATH . '/' . $table);
    }

    /**
     * Retourne une ligne
     * @return $this
     * @throws \Exception
     */
    public function find() {
        $this->outputRows = $this->applyConditions();
        if ($this->count()) {
            $this->outputRow = $this->outputRows[0];
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
        if (self::exists($table) === false) {
            throw new \Exception('Table "'. $table . '" not found');
        }
        $self = new self();
        // Nom de la table
        $self->table = $table;
        // Configuration de la table
        $self->configuration = json_decode(file_get_contents(self::PATH . '/' . $table . '/conf.json'), true);
        // Ligne vide
        $self->outputRow = new \stdClass();
        $self->outputRow->id = 0;
        foreach ($self->configuration['columns'] as $column => $type) {
            $self->outputRow->{$column} = null;
        }
        // Lignes de la table
        foreach (new \DirectoryIterator(self::PATH . '/' . $table) as $file) {
            if ($file->getExtension() === 'json' AND $file->getFilename() !== 'conf.json') {
                $row = json_decode(file_get_contents(self::PATH . '/' . $table . '/' . $file->getFilename()));
                $row->id = (int) $file->getBasename('.json');
                $self->tableRows[] = $row;
            }
        }
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
     * Enregistre une ligne
     * @return $this
     * @throws \Exception
     */
    public function save() {
        // Ajoute un id lors d'une insertion
        if ($this->outputRow->id === 0) {
            $this->outputRow->id = $this->configuration['increment']++;
        }
        // Incrémente la valeur d'incrémentation de la table
        if (file_put_contents(self::PATH . '/' . $this->table . '/conf.json', json_encode($this->configuration, JSON_PRETTY_PRINT)) === false) {
            throw new \Exception('Unable to edit the configuration file of the "' . $this->table . '" table');
        }
        // Enregistre la ligne
        $outputRow = clone $this->outputRow;
        unset($outputRow->id);
        if (file_put_contents(self::PATH . '/' . $this->table . '/' . $this->outputRow->id . '.json', json_encode($outputRow, JSON_PRETTY_PRINT)) === false) {
            throw new \Exception('Can not insert the row "' . $this->outputRow->id . '"');
        }
        return $this;
    }
    
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