<?php
// backend/middleware/RoleMiddleware.php

class RoleMiddleware {
    
    public static function requireRole($roles) {
        return function() use ($roles) {
            $user = Flight::get('user');
            
            if (!$user) {
                Flight::json([
                    'success' => false,
                    'error' => 'User not authenticated'
                ], 401);
                return false;
            }
            
            if (!in_array($user->role, $roles)) {
                Flight::json([
                    'success' => false,
                    'error' => 'Insufficient permissions',
                    'message' => 'Required role: ' . implode(', ', $roles) . 
                                '. Your role: ' . $user->role
                ], 403);
                return false;
            }
            
            return true;
        };
    }
    
    // Convenience methods
    public static function requireAdmin() {
        return self::requireRole(['admin']);
    }
    
    public static function requireUserOrAdmin() {
        return self::requireRole(['user', 'admin']);
    }
}