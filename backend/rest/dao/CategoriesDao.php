<?php
require_once __DIR__ . "/BaseDao.php";

class CategoriesDao extends BaseDao {
    public function __construct() {
        parent::__construct('categories');
    }

    // CREATE
    public function createCategory($name) {
        return $this->insert(['name' => $name]);
    }

    // READ
    public function getByName($name) {
        $stmt = $this->connection->prepare("SELECT * FROM categories WHERE name = :name");
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt->fetch();
    }

    // READ - svi
    public function getAll() {
        $stmt = $this->connection->prepare("SELECT * FROM categories");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // UPDATE
    public function updateCategory($id, $data) {
        return $this->update($id, $data);
    }

    // DELETE
    public function deleteCategory($id) {
        return $this->delete($id);
    }
}
?>
