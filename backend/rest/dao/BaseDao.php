<?php
namespace rest\dao;

require_once(__DIR__ . "/../../config.php");

class BaseDao {
   protected $table;
   protected $connection;

   public function __construct($table) {
       $this->table = $table;
       $this->connection = \Database::connect();
   }

   public function getAll() {
       try {
           $stmt = $this->connection->prepare("SELECT * FROM " . $this->table);
           $stmt->execute();
           $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
           return $result ? $result : [];
       } catch (\PDOException $e) {
           throw new \Exception("Database error in getAll: " . $e->getMessage());
       }
   }

   public function getById($id) {
       try {
           $stmt = $this->connection->prepare("SELECT * FROM " . $this->table . " WHERE id = :id");
           $stmt->bindParam(':id', $id);
           $stmt->execute();
           $result = $stmt->fetch(\PDO::FETCH_ASSOC);
           return $result ? $result : null;
       } catch (\PDOException $e) {
           throw new \Exception("Database error in getById: " . $e->getMessage());
       }
   }

   public function insert($data) {
       try {
           $columns = implode(", ", array_keys($data));
           $placeholders = ":" . implode(", :", array_keys($data));
           $sql = "INSERT INTO " . $this->table . " ($columns) VALUES ($placeholders)";
           $stmt = $this->connection->prepare($sql);
           $success = $stmt->execute($data);
           
           if ($success) {
               return $this->connection->lastInsertId();
           }
           return false;
       } catch (\PDOException $e) {
           throw new \Exception("Database error in insert: " . $e->getMessage());
       }
   }

   public function update($id, $data) {
       try {
           $fields = "";
           foreach ($data as $key => $value) {
               $fields .= "$key = :$key, ";
           }
           $fields = rtrim($fields, ", ");
           $sql = "UPDATE " . $this->table . " SET $fields WHERE id = :id";
           $stmt = $this->connection->prepare($sql);
           $data['id'] = $id;
           return $stmt->execute($data);
       } catch (\PDOException $e) {
           throw new \Exception("Database error in update: " . $e->getMessage());
       }
   }

   public function delete($id) {
       try {
           $stmt = $this->connection->prepare("DELETE FROM " . $this->table . " WHERE id = :id");
           $stmt->bindParam(':id', $id);
           return $stmt->execute();
       } catch (\PDOException $e) {
           throw new \Exception("Database error in delete: " . $e->getMessage());
       }
   }
}
?>