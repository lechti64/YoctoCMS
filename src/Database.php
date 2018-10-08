<?php

namespace Yocto;

class Database {

    /**
     * PRIVATE PROPERTIES
     */

    /** @var string Database path */
    private $path = ROOT . '/content/data';

    /** @var array Current row */
    private $row = [];

    /**
     * PUBLIC METHODS
     */

    /**
     * Delete table, row or column
     * @param string $table Table
     * @param null|string $row Row
     * @param null|string $column Colomn
     * @throws \Exception
     */
    public function delete($table, $row = null, $column = null) {
        // Check the arguments and defined $this->row
        $this->check($table, $row, $column);
        // Delete column
        if($column) {
            unset($this->row[$column]);
        }
        else {
            // Delete row
            if($row) {
                if(unlink($this->path . '/' . $table . '/' . $row . '.json') === false) {
                    throw new \Exception('Row "' . $row . '" has not been deleted');
                }
            }
            else {
                // Delete table
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
     * Insert row
     * @param string $table Table
     * @param string $row Row
     * @param array $data Data of the row
     * @throws \Exception
     */
    public function insert($table, $row, $data) {
        // Create table
        if(is_dir($table) === false AND mkdir($this->path . '/' . $table) === false) {
            throw new \Exception('Table "' . $table . '" was not created');
        }
        // Insert row
        if(file_put_contents($this->path . '/' . $table . '/' . $row . '.json', json_encode($data, JSON_PRETTY_PRINT)) === false) {
            throw new \Exception('Row "' . $row . '" was not inserted');
        }
    }

    /**
     * Select table, row or col
     * @param string $table Table
     * @param null|string $column Column
     * @param null|string $row Row
     * @return string|array
     * @throws \Exception
     */
    public function select($table, $row = null, $column = null) {
        // Check the arguments and defined $this->row
        $this->check($table, $row, $column);
        // Select column
        if($column) {
            return $this->row[$column];
        }
        // Select row
        if($row) {
            return $this->row;
        }
        // Select table
        $rows = [];
        foreach(new \DirectoryIterator($this->path . '/' . $table) as $item) {
            if($item->getExtension() === '.json') {
                $data[$item->getBasename('.json')] = file_get_contents($this->path . '/' . $table . '/' . $item->getFilename());
            }
        }
        return $rows;
    }

    /**
     * Update column
     * @param string $table Table
     * @param string $row Row
     * @param string $column Column
     * @param array $data Data of the column
     * @throws \Exception
     */
    public function update($table, $row, $column, $data) {
        // Check the arguments and defined $this->row
        $this->check($table, $row, $column);
        // Update column
        $this->row[$column] = $data;
        if(file_put_contents($this->path . '/' . $table . '/' . $row . '.json', json_encode($this->row, JSON_PRETTY_PRINT)) === false) {
            throw new \Exception('Line "' . $row . '" has not been updated');
        }
    }

    /**
     * PRIVATE METHODS
     */

    /**
     * Check the arguments and defined $this->row
     * @param string $table Table
     * @param null|string $row Row
     * @param null|string $column Colomn
     * @throws \Exception
     */
    private function check($table, $row = null, $column = null) {
        // Table not found
        if(is_dir($this->path . '/' . $table) === false) {
            throw new \Exception('Table "'. $table . '" not found');
        }
        // Row not found
        if($row) {
            if(is_file($this->path . '/' . $table . '/' . $row . '.json')) {
                $this->row = json_decode(file_get_contents($this->path . '/' . $table . '/' . $row . '.json'), true);
            }
            else {
                throw new \Exception('Row "'. $row . '" not found');
            }
        }
        // Column not found
        if($column AND isset($this->row[$column]) === false) {
            throw new \Exception('Column "'. $column . '" not found');
        }
    }

}