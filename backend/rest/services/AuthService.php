<?php
namespace rest\services;

require_once __DIR__ . '/BaseServices.php';
require_once __DIR__ . '/../dao/AuthDao.php';
require_once __DIR__ . '/JWTService.php';

class AuthService extends BaseServices {
    private $jwtService;

    public function __construct() {
        parent::__construct(new \rest\dao\AuthDao());
        $this->jwtService = new JWTService();
    }

    public function register($data) {
        try {
            $this->validateData($data, 'create');
            
            // Provjeri da li korisnik već postoji
            $existingUser = $this->dao->get_user_by_email($data['email']);
            if ($existingUser) {
                throw new \Exception("User with this email already exists");
            }

            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Pripremi podatke za insert
            $userData = [
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $hashedPassword,
                'role' => 'user',
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Ubaci korisnika
            $userId = $this->dao->insert($userData);

            return [
                'success' => true,
                'message' => 'User registered successfully',
                'user_id' => $userId
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function login($data) {
        try {
            $this->validateData($data, 'login');
            
            // Dohvati korisnika po emailu
            $user = $this->dao->get_user_by_email($data['email']);
            
            if (!$user) {
                throw new \Exception("Invalid email or password");
            }

            // Provjeri password
            if (!password_verify($data['password'], $user['password'])) {
                throw new \Exception("Invalid email or password");
            }

            // Ukloni password iz odgovora
            unset($user['password']);

            // Kreiraj JWT token
            $token = $this->jwtService->create([
                'user_id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role'] ?? 'user',
                'username' => $user['username']
            ]);

            return [
                'success' => true,
                'token' => $token,
                'user' => $user
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    protected function validateData($data, $action) {
        parent::validateData($data, $action);
        
        if ($action == 'create' || $action == 'register') {
            if (!isset($data['email']) || empty(trim($data['email']))) {
                throw new \Exception("Email is required");
            }
            if (!isset($data['password']) || empty(trim($data['password']))) {
                throw new \Exception("Password is required");
            }
            if (!isset($data['username']) || empty(trim($data['username']))) {
                throw new \Exception("Username is required");
            }
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("Invalid email format");
            }
            if (strlen($data['password']) < 6) {
                throw new \Exception("Password must be at least 6 characters");
            }
        }
        
        if ($action == 'login') {
            if (!isset($data['email']) || empty(trim($data['email']))) {
                throw new \Exception("Email is required");
            }
            if (!isset($data['password']) || empty(trim($data['password']))) {
                throw new \Exception("Password is required");
            }
        }
    }
}