<?php

namespace Yocto;

class Database {

    /**
     * PROPRIÉTÉS PRIVÉES
     */

    /** @var string Chemin vers la base de données */
    private $path = ROOT . '/content/data';

    /** @var array Ligne courante */
    private $row = [];

    /**
     * MÉTHODES PUBLIQUES
     */

    /**
     * Supprime une table, ligne ou colonne
     * @param string $table Table
     * @param null|string $row Ligne
     * @param null|string $column Colonne
     * @throws \Exception
     */
    public function delete($table, $row = null, $column = null) {
        // Check les arguments et ajoute des lignes dans la propriété $this->row
        $this->check($table, $row, $column);
        // Supprime la colonne
        if($column) {
            unset($this->row[$column]);
        }
        else {
            // Supprime la ligne
            if($row) {
                if(unlink($this->path . '/' . $table . '/' . $row . '.json') === false) {
                    throw new \Exception('Row "' . $row . '" has not been deleted');
                }
            }
            // Supprime la table
            else {
                if(empty(array_map('unlink', glob($this->path . '/' . $table . '/*.json'))) === false) {
                    throw new \Exception('Rows have not been deleted in the table "' . $table . '"');
                }
                if(rmdir($this->path . '/' . $table) === false) {
                    throw new \Exception('Table "' . $table . '" has not been deleted');
                }
            }
        }
    }

    /**
     * Insert une ligne
     * @param string $table Table
     * @param string $row Ligne
     * @param array $data Données de la ligne
     * @throws \Exception
     */
    public function insert($table, $row, $data) {
        // Crée la table
        if(is_dir($table) === false AND mkdir($this->path . '/' . $table) === false) {
            throw new \Exception('Table "' . $table . '" was not created');
        }
        // Insert la ligne
        if(file_put_contents($this->path . '/' . $table . '/' . $row . '.json', json_encode($data, JSON_PRETTY_PRINT)) === false) {
            throw new \Exception('Row "' . $row . '" was not inserted');
        }
    }

    /**
     * Sélectionne une table, ligne ou colonne
     * @param string $table Table
     * @param null|string $column Colonne
     * @param null|string $row Ligne
     * @return string|array
     * @throws \Exception
     */
    public function select($table, $row = null, $column = null) {
        // Check les arguments et ajoute des lignes dans la propriété $this->row
        $this->check($table, $row, $column);
        // Sélectionne la colonne
        if($column) {
            return $this->row[$column];
        }
        // Sélectionne la ligne
        if($row) {
            return $this->row;
        }
        // Sélectionne la table
        $rows = [];
        foreach(new \DirectoryIterator($this->path . '/' . $table) as $item) {
            if($item->getExtension() === '.json') {
                $data[$item->getBasename('.json')] = file_get_contents($this->path . '/' . $table . '/' . $item->getFilename());
            }
        }
        return $rows;
    }

    /**
     * Met à jour une colonne
     * @param string $table Table
     * @param string $row Ligne
     * @param string $column Colonne
     * @param array $data Données de la colonne
     * @throws \Exception
     */
    public function update($table, $row, $column, $data) {
        // Check les arguments et ajoute des lignes dans la propriété $this->row
        $this->check($table, $row, $column);
        // Met à jour la colonne
        $this->row[$column] = $data;
        if(file_put_contents($this->path . '/' . $table . '/' . $row . '.json', json_encode($this->row, JSON_PRETTY_PRINT)) === false) {
            throw new \Exception('Line "' . $row . '" has not been updated');
        }
    }

    /**
     * MÉTHODES PRIVÉES
     */

    /**
     * Check les arguments et ajoute des lignes dans la propriété $this->row
     * @param string $table Table
     * @param null|string $row Ligne
     * @param null|string $column Colonne
     * @throws \Exception
     */
    private function check($table, $row = null, $column = null) {
        // Table introuvable
        if(is_dir($this->path . '/' . $table) === false) {
            throw new \Exception('Table "'. $table . '" not found');
        }
        // Ligne introuvable
        if($row) {
            if(is_file($this->path . '/' . $table . '/' . $row . '.json')) {
                $this->row = json_decode(file_get_contents($this->path . '/' . $table . '/' . $row . '.json'), true);
            }
            else {
                throw new \Exception('Row "'. $row . '" not found');
            }
        }
        // Colonne introuvable
        if($column AND isset($this->row[$column]) === false) {
            throw new \Exception('Column "'. $column . '" not found');
        }
    }

}