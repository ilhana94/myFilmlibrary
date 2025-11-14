<?php
require_once __DIR__ . '/BaseServices.php';
require_once __DIR__ . '/../dao/CategoriesDao.php';

class CategoriesService extends BaseService {
    public function __construct() {
        parent::__construct(new CategoriesDao());
    }

    // Override generičku validaciju za kategorije
    protected function validateData($data, $action) {
        parent::validateData($data, $action); // poziv generičke validacije

        // Specifična pravila za kategorije
        if(!isset($data['name']) || trim($data['name']) === '') {
            throw new Exception("Category name is required");
        }

        if(strlen($data['name']) > 50) {
            throw new Exception("Category name cannot be longer than 50 characters");
        }

        // Eventualno provjeriti duplikate u bazi
        // $existing = $this->dao->getByName($data['name']);
        // if($existing && $action === 'create') throw new Exception("Category name already exists");
    }
}
?>
