<?php

namespace Ninja;

class DatabaseTable {

    public function __construct(
        private \PDO $pdo, 
        private string $table, 
        private string $primaryKey,
        private string $className = '\stdClass', 
        private array $constructorArgs = []
    ) {
    }

    public function total(string $field = null, string $value = null) {
        $sql = 'SELECT COUNT(*) FROM `' . $this->table . '`';
        $parameters = [];

        if (!empty($field)) {
            $sql .= ' WHERE `' . $field . '` = :value';
            $parameters = ['value' => $value];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parameters);
        $row = $stmt->fetch();
        return $row[0];
    }

    public function find(string $field, string $value, string $orderBy = null, int $limit = 0, int $offset = 0) {
        $query = 'SELECT * FROM `' . $this->table . '` WHERE `' . $field . '` = :value';

        $values = [
            'value' => $value
        ];

        if ($orderBy != null) {
            $query .= ' ORDER BY ' . $orderBy;
        }

        if ($limit > 0) {
            $query .= ' LIMIT ' . $limit;
        }

        if ($offset > 0) {
            $query .= ' OFFSET ' . $offset;
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($values);

        return $stmt->fetchAll(
            \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 
            $this->className, 
            $this->constructorArgs
        );
    }

    public function findAll(string $orderBy = null, int $limit = 0, int $offset = 0) {
        $query = 'SELECT * FROM `' . $this->table . '`';

        if ($orderBy != null) {
            $query .= ' ORDER BY ' . $orderBy;
        }

        if ($limit > 0) {
            $query .= ' LIMIT ' . $limit;
        }

        if ($offset > 0) {
            $query .= ' OFFSET ' . $offset;
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(
            \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 
            $this->className, 
            $this->constructorArgs
        );
    }

    private function insert($values) {
        $query = 'INSERT INTO `' . $this->table . '` (';

        foreach ($values as $key => $value) {
            $query .= '`' . $key . '`,';
        }

        $query = rtrim($query, ',');

        $query .= ') VALUES (';

        foreach ($values as $key => $value) {
            $query .= ':' . $key . ',';
        }

        $query = rtrim($query, ',');

        $query .= ')';

        $values = $this->processDates($values);

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($values);

        return $this->pdo->lastInsertId();
    }

    private function update($values) {
        $query = ' UPDATE `' . $this->table .'` SET ';

        foreach ($values as $key => $value) {
            $query .= '`' . $key . '` = :' . $key . ',';
        }

        $query = rtrim($query, ',');

        $query .= ' WHERE `' . $this->primaryKey . '` = :primaryKey';

        // Set the :primaryKey variable
        $values['primaryKey'] = $values['id'];

        $values = $this->processDates($values);

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($values);
    }


    public function delete($field, $value) {
        $values = [':value' => $value];

        $stmt = $this->pdo->prepare('DELETE FROM `' . $this->table . '` WHERE `' . $field . '` = :value');
        $stmt->execute($values);
    }

    private function processDates($values) {
        foreach ($values as $key => $value) {
            if ($value instanceof \DateTime) {
                $values[$key] = $value->format('Y-m-d');
            }
        }

        return $values;
    }

    public function save($record) {
        $entity = new $this->className(...$this->constructorArgs);

        try {
           if (empty($record[$this->primaryKey])) {
             unset($record[$this->primaryKey]);
           }

           $insertId = $this->insert($record);

           $entity->{$this->primaryKey} = $insertId;
        } catch (\PDOException $e) {
            $this->update($record);
        }

        foreach ($record as $key => $value) {
            if (!empty($value)) {
                if ($value instanceof \DateTime) {
                    $value = $value->format('Y-m-d H:i:s');
                } 
                $entity->$key = $value;
                
            }
        }

        return $entity;
     }

}