<?php
namespace rest\services;

require_once __DIR__ . '/BaseServices.php';
require_once __DIR__ . '/../dao/UsersDao.php';

class UserServices extends BaseServices {  
    public function __construct() {
        parent::__construct(new \rest\dao\UsersDao());  
    }

    protected function validateData($data, $action) {
        parent::validateData($data, $action);

        if(!isset($data['username']) || trim($data['username']) === '') {
            throw new \Exception("Username is required");  
        }
        if(strlen($data['username']) > 50) {
            throw new \Exception("Username cannot be longer than 50 characters");
        }

        if(!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Valid email is required");
        }

        if($action === 'create') {
            if(!isset($data['password']) || strlen($data['password']) < 6) {
                throw new \Exception("Password must be at least 6 characters");
            }
        }
    }
}
?>