<?php
// backend/services/JWTService.php

class JWTService {
    private $secret = 'film_library_jwt_secret_2025_change_in_production';
    private $algorithm = 'HS256';
    
    public function generateToken($userData) {
        $payload = [
            'iat' => time(), // Issued at
            'exp' => time() + (24 * 60 * 60), // Expires in 24 hours
            'data' => $userData
        ];
        
        return Firebase\JWT\JWT::encode($payload, $this->secret, $this->algorithm);
    }
    
    public function validateToken($token) {
        try {
            $decoded = Firebase\JWT\JWT::decode(
                $token, 
                new Firebase\JWT\Key($this->secret, $this->algorithm)
            );
            
            // Additional validation
            if (!isset($decoded->data->id)) {
                throw new Exception('Invalid token payload');
            }
            
            return $decoded;
            
        } catch (\Firebase\JWT\ExpiredException $e) {
            throw new Exception('Token expired: ' . $e->getMessage());
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            throw new Exception('Invalid token signature');
        } catch (Exception $e) {
            throw new Exception('Token validation failed: ' . $e->getMessage());
        }
    }
    
    public function getUserFromToken($token) {
        try {
            $decoded = $this->validateToken($token);
            return $decoded->data;
        } catch (Exception $e) {
            return null;
        }
    }
}