<?php
namespace rest\services;  // 👈 DODAJ OVAJ NAMESPACE

class CategoriesService extends BaseServices {  // 👈 Promijeni u "BaseServices" (s na kraju)
    public function __construct() {
        parent::__construct(new \rest\dao\CategoriesDao());  // 👈 Dodaj namespace
    }

    // Override generičku validaciju za kategorije
    protected function validateData($data, $action) {
        parent::validateData($data, $action); // poziv generičke validacije

        // Specifična pravila za kategorije
        if(!isset($data['name']) || trim($data['name']) === '') {
            throw new \Exception("Category name is required");  // 👈 Dodaj backslash
        }

        if(strlen($data['name']) > 50) {
            throw new \Exception("Category name cannot be longer than 50 characters");  // 👈 Dodaj backslash
        }

        // Eventualno provjeriti duplikate u bazi
        // $existing = $this->dao->getByName($data['name']);
        // if($existing && $action === 'create') throw new Exception("Category name already exists");
    }
}
?>