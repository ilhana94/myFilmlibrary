<?php
// backend/middleware/AuthMiddleware.php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../data/roles.php'; // Uključite roles.php

class AuthMiddleware {
   
   /**
    * Verifikuj JWT token
    */
   public function verifyToken($token){
       if(!$token) {
           Flight::halt(401, json_encode([
               'success' => false,
               'message' => "Missing authentication header",
               'code' => 'MISSING_TOKEN'
           ]));
       }
       
       try {
           // Ukloni "Bearer " prefix ako postoji
           $token = str_replace('Bearer ', '', $token);
           
           $decoded_token = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));
           
           // Postavi user podatke
           Flight::set('user', $decoded_token->user);
           Flight::set('user_id', $decoded_token->user->id);
           Flight::set('user_role', $decoded_token->user->role);
           Flight::set('jwt_token', $token);
           
           return TRUE;
       } catch (\Exception $e) {
           Flight::halt(401, json_encode([
               'success' => false,
               'message' => "Invalid token: " . $e->getMessage(),
               'code' => 'INVALID_TOKEN'
           ]));
       }
   }
   
   /**
    * Autorizuj pristup za specifičnu rolu
    */
   public function authorizeRole($requiredRole) {
       $user = Flight::get('user');
       
       if (!$user) {
           Flight::halt(401, json_encode([
               'success' => false,
               'message' => 'Authentication required',
               'code' => 'NOT_AUTHENTICATED'
           ]));
       }
       
       // Koristite Roles klasu za provjeru hijerarhije
       if (!Roles::hasAccess($user->role, $requiredRole)) {
           Flight::halt(403, json_encode([
               'success' => false,
               'message' => 'Access denied: insufficient privileges. Required role: ' . $requiredRole,
               'user_role' => $user->role,
               'required_role' => $requiredRole,
               'code' => 'INSUFFICIENT_PRIVILEGES'
           ]));
       }
       
       return true;
   }
   
   /**
    * Autorizuj pristup za više rola
    */
   public function authorizeRoles($allowedRoles) {
       $user = Flight::get('user');
       
       if (!$user) {
           Flight::halt(401, json_encode([
               'success' => false,
               'message' => 'Authentication required',
               'code' => 'NOT_AUTHENTICATED'
           ]));
       }
       
       // Provjeri da li korisnik ima jednu od dozvoljenih rola
       foreach ($allowedRoles as $allowedRole) {
           if (Roles::hasAccess($user->role, $allowedRole)) {
               return true;
           }
       }
       
       Flight::halt(403, json_encode([
           'success' => false,
           'message' => 'Forbidden: role not allowed. Allowed roles: ' . implode(', ', $allowedRoles),
           'user_role' => $user->role,
           'allowed_roles' => $allowedRoles,
           'code' => 'ROLE_NOT_ALLOWED'
       ]));
   }
   
   /**
    * Autorizuj na osnovu permisije
    */
   public function authorizePermission($requiredPermission) {
       $user = Flight::get('user');
       
       if (!$user) {
           Flight::halt(401, json_encode([
               'success' => false,
               'message' => 'Authentication required',
               'code' => 'NOT_AUTHENTICATED'
           ]));
       }
       
       // Ako user nema permissions array, dohvatite iz baze
       if (!isset($user->permissions) || !is_array($user->permissions)) {
           $userPermissions = $this->getUserPermissions($user->id);
       } else {
           $userPermissions = $user->permissions;
       }
       
       if (!in_array($requiredPermission, $userPermissions)) {
           Flight::halt(403, json_encode([
               'success' => false,
               'message' => 'Access denied: permission missing: ' . $requiredPermission,
               'user_permissions' => $userPermissions,
               'required_permission' => $requiredPermission,
               'code' => 'MISSING_PERMISSION'
           ]));
       }
       
       return true;
   }
   
   /**
    * Autorizuj pristup resursu (vlasništvo)
    * Provjeri da li je korisnik vlasnik resursa ili admin
    */
   public function authorizeResource($resourceUserId) {
       $user = Flight::get('user');
       
       if (!$user) {
           Flight::halt(401, json_encode([
               'success' => false,
               'message' => 'Authentication required',
               'code' => 'NOT_AUTHENTICATED'
           ]));
       }
       
       // Admini mogu sve
       if ($user->role === Roles::ADMIN) {
           return true;
       }
       
       // Provjeri da li je korisnik vlasnik resursa
       if ($user->id != $resourceUserId) {
           Flight::halt(403, json_encode([
               'success' => false,
               'message' => 'Access denied: you are not the owner of this resource',
               'user_id' => $user->id,
               'resource_owner_id' => $resourceUserId,
               'code' => 'NOT_OWNER'
           ]));
       }
       
       return true;
   }
   
   /**
    * Autorizuj pristup resursu sa opcijom za dodatne role
    */
   public function authorizeResourceWithRoles($resourceUserId, $allowedRoles = []) {
       $user = Flight::get('user');
       
       if (!$user) {
           Flight::halt(401, 'Authentication required');
       }
       
       // Provjeri da li je admin
       if ($user->role === Roles::ADMIN) {
           return true;
       }
       
       // Provjeri da li je u allowedRoles
       if (!empty($allowedRoles) && in_array($user->role, $allowedRoles)) {
           return true;
       }
       
       // Provjeri vlasništvo
       if ($user->id == $resourceUserId) {
           return true;
       }
       
       Flight::halt(403, json_encode([
           'success' => false,
           'message' => 'Access denied: insufficient permissions for this resource',
           'code' => 'RESOURCE_ACCESS_DENIED'
       ]));
   }
   
   /**
    * Dohvati permisije korisnika iz baze
    */
   private function getUserPermissions($userId) {
       try {
           // Pretpostavljamo da imate 'permissions' tabelu ili sistem permisija
           $stmt = Flight::db()->prepare("
               SELECT p.name 
               FROM user_permissions up
               JOIN permissions p ON up.permission_id = p.id
               WHERE up.user_id = :user_id
               UNION
               SELECT p.name 
               FROM role_permissions rp
               JOIN permissions p ON rp.permission_id = p.id
               JOIN users u ON u.role = rp.role_name
               WHERE u.id = :user_id
           ");
           
           $stmt->execute([':user_id' => $userId]);
           $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
           
           return $permissions ?: [];
       } catch (Exception $e) {
           // Ako nema permisija tabele, vrati default permisije bazirane na roli
           return $this->getDefaultPermissions(Flight::get('user_role'));
       }
   }
   
   /**
    * Default permisije za role
    */
   private function getDefaultPermissions($role) {
       $defaultPermissions = [
           Roles::ADMIN => [
               'movies.create', 'movies.read', 'movies.update', 'movies.delete',
               'users.create', 'users.read', 'users.update', 'users.delete',
               'reviews.create', 'reviews.read', 'reviews.update', 'reviews.delete',
               'categories.manage'
           ],
           Roles::MODERATOR => [
               'movies.create', 'movies.read', 'movies.update',
               'users.read',
               'reviews.create', 'reviews.read', 'reviews.update', 'reviews.delete',
               'categories.read'
           ],
           Roles::USER => [
               'movies.read',
               'reviews.create', 'reviews.read', 'reviews.update_own', 'reviews.delete_own',
               'profile.read', 'profile.update'
           ],
           Roles::GUEST => [
               'movies.read',
               'reviews.read'
           ]
       ];
       
       return $defaultPermissions[$role] ?? ['movies.read'];
   }
   
   /**
    * Globalni middleware handler za sve rute
    */
   public function handle() {
       $request = Flight::request();
       $url = $request->url;
       
       // Javne rute koje ne zahtijevaju autentikaciju
       $publicRoutes = [
           '/auth/login',
           '/auth/register',
           '/auth/verify',
           '/',
           '/docs',
           '/health',
           '/api-docs'
       ];
       
       // Provjeri da li je ruta javna
       foreach ($publicRoutes as $publicRoute) {
           if (strpos($url, $publicRoute) === 0) {
               return true;
           }
       }
       
       // Za sve zaštićene rute, provjeri token
       $headers = getallheaders();
       $token = $headers['Authorization'] ?? $headers['authorization'] ?? '';
       
       if (!$token) {
           Flight::halt(401, json_encode([
               'success' => false,
               'message' => 'Authorization header required',
               'code' => 'AUTH_HEADER_REQUIRED'
           ]));
       }
       
       return $this->verifyToken($token);
   }
}