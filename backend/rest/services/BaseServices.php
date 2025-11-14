<?php
require_once __DIR__ . '/BaseServices.php';


class BaseService {
    protected $dao;

    public function __construct($dao) {
        $this->dao = $dao;
    }

    public function getAll() {
        return $this->dao->getAll();
    }

    public function getById($id) {
        if(!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid ID");
        }
        return $this->dao->getById($id);
    }

    public function create($data) {
        $this->validateData($data, 'create');
        return $this->dao->insert($data);
    }

    public function update($id, $data) {
        if(!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid ID");
        }
        $this->validateData($data, 'update');
        return $this->dao->update($id, $data);
    }

    public function delete($id) {
        if(!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid ID");
        }
        return $this->dao->delete($id);
    }

    // Osnovna validacija, može se proširiti u child servisima
    protected function validateData($data, $action) {
        if(!is_array($data) || empty($data)) {
            throw new Exception("Data must be a non-empty array for $action");
        }

        // Ovo je generički primjer - child klase mogu override
        if(isset($data['name']) && strlen($data['name']) > 100) {
            throw new Exception("Name cannot be longer than 100 characters");
        }
    }
}
?>
