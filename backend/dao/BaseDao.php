<?php
// backend/rest/dao/BaseDao.php

class BaseDao {
    protected $conn;
    protected $table_name;

    public function __construct($table_name) {
        $this->table_name = $table_name;
        $this->conn = $this->get_connection();
    }

    protected function get_connection() {
        // Koristi Flight::db() ako je dostupan, inače kreiraj novu konekciju
        if (class_exists('Flight') && Flight::has('db')) {
            return Flight::db();
        }
        
        // Ako Flight nije dostupan, kreiraj PDO konekciju direktno
        try {
            return new PDO(
                "mysql:host=localhost;dbname=film_library;charset=utf8mb4",
                "root",
                ""
            );
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function get_all() {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_by_id($id) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($entity) {
        $columns = implode(', ', array_keys($entity));
        $placeholders = ':' . implode(', :', array_keys($entity));
        
        $sql = "INSERT INTO {$this->table_name} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($entity);
        
        return $this->conn->lastInsertId();
    }

    public function update($id, $entity) {
        $columns = '';
        foreach ($entity as $key => $value) {
            $columns .= $key . ' = :' . $key . ', ';
        }
        $columns = rtrim($columns, ', ');
        
        $entity['id'] = $id;
        $sql = "UPDATE {$this->table_name} SET {$columns} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($entity);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table_name . " WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function query_unique($query, $params) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function query($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}