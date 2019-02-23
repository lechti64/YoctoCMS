<?php

namespace Yocto;

class Database implements \IteratorAggregate, \Countable
{

    const PATH = ROOT . '/content/data';

    /** @var array Conditions */
    private $conditions = [
        'limit' => [],
        'orderBy' => [],
        'where' => [],
    ];

    /** @var array Configuration */
    private $configuration = [];

    /** @var \stdClass Ligne lors d'un find, insert ou update */
    private $row;

    /** @var array Lignes */
    private $rows = [];

    /** @var string Table */
    private $table = '';

    /**
     * Retourne une valeur lors d'un find, insert ou update
     * @param string $column Colonne
     * @return mixed
     */
    public function __get($column)
    {
        return $this->row->{$column};
    }

    /**
     * Insert une valeur lors d'un find, insert ou update
     * @param string $column Colonne
     * @param mixed $value Valeur
     * @throws \Exception
     */
    public function __set($column, $value)
    {
        if (array_key_exists($column, $this->configuration['columns'])) {
            $this->row->{$column} = $this->filter($value, $this->configuration['columns'][$column]);
        } else {
            throw new \Exception('Column "' . $column . '" not found in "' . $this->table . '" table');
        }
    }

    /**
     * Alias de where
     * @param string $column Colonne
     * @param string $comparisonOperators Opérateur de comparaison (=, !=, >, >=, <, <=, IN, NOT IN, LIKE)
     * @param string $value Valeur
     * @return $this
     */
    public function andWhere($column, $comparisonOperators, $value)
    {
        $this->where($column, $comparisonOperators, $value);
        return $this;
    }

    /**
     * Compte le nombre de lignes
     * @return int
     */
    public function count()
    {
        return count($this->rows);
    }

    /**
     * Crée une table
     * @param string $table Table
     * @param array $columns Colonne et type de données
     * @throws \Exception
     */
    public static function create($table, array $columns = [])
    {
        // Crée la table
        if (self::exists($table) === false AND mkdir(self::PATH . '/' . $table) === false) {
            throw new \Exception('Failed to save "' . $table . '" table');
        }
        // Crée le fichier de configuration
        $configuration = [
            'columns' => $columns,
            'increment' => 1,
        ];
        if (file_put_contents(self::PATH . '/' . $table . '/conf.json', json_encode($configuration, JSON_PRETTY_PRINT)) === false) {
            throw new \Exception('Failed to save "conf.json" for "' . $table . '" table');
        }
    }

    /**
     * Supprime la table, une ligne ou des lignes
     * @return bool
     * @throws \Exception
     */
    public function delete()
    {
        // Supprime une ligne
        if ($this->row->id) {
            if (
                is_file(self::PATH . '/' . $this->table . '/' . $this->row->id . '.json')
                AND unlink(self::PATH . '/' . $this->table . '/' . $this->row->id . '.json') === false
            ) {
                throw new \Exception('Failed to delete "' . $this->row->id . '" row');
            }
        } // Supprime des lignes
        else if ($this->rows) {
            foreach ($this->rows as $row) {
                if (
                    is_file(self::PATH . '/' . $this->table . '/' . $row->id . '.json')
                    AND unlink(self::PATH . '/' . $this->table . '/' . $row->id . '.json') === false
                ) {
                    throw new \Exception('Failed to delete "' . $row->id . '" row');
                }
            }
        } // Supprime la table
        else {
            if (in_array(false, array_map('unlink', glob(self::PATH . '/' . $this->table . '/*.json')))) {
                throw new \Exception('Row have not been deleted in "' . $this->table . '" table');
            }
            if (rmdir(self::PATH . '/' . $this->table) === false) {
                throw new \Exception('Failed to delete "' . $this->table . '" table');
            }
        }
        return true;
    }

    /**
     * Test l'existence d'une table
     * @param string $table Table
     * @return bool
     */
    public static function exists($table)
    {
        return is_dir(self::PATH . '/' . $table);
    }

    /**
     * Retourne une ligne
     * @return $this
     * @throws \Exception
     */
    public function find()
    {
        $this->applyConditions();
        if ($this->count()) {
            $this->row = $this->rows[0];
        }
        return $this;
    }

    /**
     * Retourne toutes les lignes
     * @return $this
     * @throws \Exception
     */
    public function findAll()
    {
        $this->applyConditions();
        return $this;
    }

    /**
     * Crée l'itérateur
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->rows);
    }

    /**
     * Crée l'instance d'une table
     * @param string $table Table
     * @return Database
     * @throws \Exception
     */
    public static function instance($table)
    {
        if (self::exists($table) === false) {
            throw new \Exception('"' . $table . '" table not found');
        }
        $self = new self();
        // Table instanciée
        $self->table = $table;
        // Configuration de la table
        $self->configuration = json_decode(file_get_contents(self::PATH . '/' . $table . '/conf.json'), true);
        // Ligne vide
        $self->row = new \stdClass();
        $self->row->id = 0;
        foreach ($self->configuration['columns'] as $column => $type) {
            $self->row->{$column} = $self->filter('', $self->configuration['columns'][$column]);
        }
        // Lignes de la table
        foreach (new \DirectoryIterator(self::PATH . '/' . $table) as $file) {
            if ($file->getExtension() === 'json' AND $file->getFilename() !== 'conf.json') {
                $row = json_decode(file_get_contents(self::PATH . '/' . $table . '/' . $file->getFilename()));
                $row->id = (int)$file->getBasename('.json');
                $self->rows[] = $row;
            }
        }
        return $self;
    }

    /**
     * Ajoute condition une limit
     * @param int $offset Index de début
     * @param int $length Nombre de lignes
     * @return $this
     */
    public function limit($offset, $length)
    {
        $this->conditions['limit'] = [
            'length' => $length,
            'offset' => $offset,
        ];
        return $this;
    }

    /**
     * Ajoute une condition orderBy
     * @param string $column Colonne
     * @param string $direction Ordre de tri (ASC ou DESC)
     * @return $this
     */
    public function orderBy($column, $direction = 'ASC')
    {
        $this->conditions['orderBy'] = [
            'column' => $column,
            'direction' => $direction,
        ];
        return $this;
    }

    /**
     * Alias de where
     * @param string $column Colonne
     * @param string $comparisonOperators Opérateur de comparaison (=, !=, >, >=, <, <=, IN, NOT IN, LIKE)
     * @param string $value Valeur
     * @return $this
     */
    public function orWhere($column, $comparisonOperators, $value)
    {
        $this->where($column, $comparisonOperators, $value, 'OR');
        return $this;
    }

    /**
     * Enregistre une ligne
     * @return bool
     * @throws \Exception
     */
    public function save()
    {
        // Échec d'enregistrement si un valeur est égale à null, car null signifie qu'un champ obligatoire est vide
        if (in_array(null, (array)$this->row, true)) {
            return false;
        }
        // Ajoute un id lors d'une insertion
        if ($this->row->id === 0) {
            $this->row->id = $this->configuration['increment']++;
        }
        // Incrémente le fichier de configuration
        if (file_put_contents(self::PATH . '/' . $this->table . '/conf.json', json_encode($this->configuration, JSON_PRETTY_PRINT)) === false) {
            throw new \Exception('Failed to edit "conf.json" for "' . $this->table . '" table');
        }
        // Enregistre la ligne
        $row = clone $this->row;
        unset($row->id);
        if (file_put_contents(self::PATH . '/' . $this->table . '/' . $this->row->id . '.json', json_encode($row, JSON_PRETTY_PRINT)) === false) {
            throw new \Exception('Failed to insert "' . $this->row->id . '" row for "' . $this->table . '" table');
        }
        return true;
    }

    /**
     * Enregistre les instances demandées
     * @param Database[] $instances Instances à enregistrer
     * @return bool
     * @throws \Exception
     */
    public static function saveAll(array $instances = [])
    {
        // Bloque l'enregistrement si l'une des instances contient une ligne dont la valeur est égale à null
        foreach ($instances as $instance) {
            if (in_array(null, (array)$instance->row)) {
                return false;
            }
        }
        // Enregistre les instances
        foreach ($instances as $instance) {
            $instance->save();
        }
        return true;
    }

    /**
     * Ajoute une condition where
     * @param string $column Colonne
     * @param string $comparisonOperators Opérateur de comparaison (=, !=, >, >=, <, <=, IN, NOT IN, LIKE)
     * @param string $value Valeur
     * @param string $logicalOperator Opérateur logique (AND ou OR)
     * @return $this
     */
    public function where($column, $comparisonOperators, $value, $logicalOperator = 'AND')
    {
        $this->conditions['where'][] = [
            'column' => $column,
            'comparisonOperators' => $comparisonOperators,
            'logicalOperator' => $logicalOperator,
            'value' => $value,
        ];
        return $this;
    }

    /**
     * Applique les conditions
     * @throws \Exception
     */
    private function applyConditions()
    {
        // Applique les conditions where
        if ($this->conditions['where']) {
            $this->applyWhere();
        }
        // Applique la condition orderBy
        if ($this->conditions['orderBy']) {
            $this->applyOrderBy();
        }
        // Applique la condition limit
        if ($this->conditions['limit']) {
            ;
            $this->applyLimit();
        }
        // Réindexation
        $this->rows = array_values($this->rows);
    }

    /**
     * Applique la condition limit
     */
    private function applyLimit()
    {
        $this->rows = array_slice(
            $this->rows,
            $this->conditions['limit']['offset'],
            $this->conditions['limit']['length']
        );
    }

    /**
     * Applique la condition orderBy
     */
    private function applyOrderBy()
    {
        uasort($this->rows, function ($a, $b) {
            if ($this->conditions['orderBy']['direction'] === 'ASC') {
                return strnatcasecmp(
                    $a->{$this->conditions['orderBy']['column']},
                    $b->{$this->conditions['orderBy']['column']}
                );
            } else if ($this->conditions['orderBy']['direction'] === 'DESC') {
                return strnatcasecmp(
                    $b->{$this->conditions['orderBy']['column']},
                    $a->{$this->conditions['orderBy']['column']}
                );
            } else {
                throw new \Exception('Unknown "' . $this->conditions['orderBy'] . '" direction');
            }
        });
    }

    /**
     * Applique la condition where
     */
    private function applyWhere()
    {
        $this->rows = array_filter($this->rows, function ($row) {
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
                switch ($condition['comparisonOperators']) {
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
                        throw new \Exception('Unknown "' . $condition['logicalOperator'] . '" logical operator');
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
    private function filter($value, $type)
    {
        // Pas de filtre pour une valeur null, car null signifie qu'un champ obligatoire est vide
        if ($value === null) {
            return null;
        }
        // Filtre la valeur
        switch ($type) {
            case 'boolean':
                return (bool)$value;
            case 'float':
                return (float)$value;
            case 'integer':
                return (int)$value;
            default:
                return (string)$value;
        }
    }

}