<?php
// backend/index.php

// 1. UČITAJ CONFIG
require_once __DIR__ . '/../config.php';

// 2. UČITAJ AUTOLOADER
require_once __DIR__ . '/../vendor/autoload.php';

// 3. UČITAJ ROLES
require_once __DIR__ . '/data/roles.php';

// 4. UČITAJ SERVISE
require_once __DIR__ . '/rest/services/AuthService.php';
require_once __DIR__ . '/rest/services/JWTService.php';
require_once __DIR__ . '/rest/services/MoviesServices.php';
require_once __DIR__ . '/rest/services/UserServices.php';
require_once __DIR__ . '/rest/services/CategoriesService.php';
require_once __DIR__ . '/rest/services/ReviewsServices.php';
require_once __DIR__ . '/rest/services/MovieCategoriesServices.php';

// 5. UČITAJ MIDDLEWARE
require_once __DIR__ . '/middleware/AuthMiddleware.php';

// Omogući prikaz grešaka za development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 6. REGISTRUJ BAZU
Flight::register('db', 'PDO', array(
    "mysql:host=" . Config::DB_HOST() . ";port=" . Config::DB_PORT() . ";dbname=" . Config::DB_NAME(),
    Config::DB_USER(),
    Config::DB_PASSWORD()
));

// Postavi PDO atribute
Flight::db()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
Flight::db()->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// 7. REGISTRUJ SERVISE
Flight::register('auth_service', 'AuthService');
Flight::register('jwt_service', 'JWTService');
Flight::register('movies_service', 'MoviesServices');
Flight::register('user_service', 'UserServices');
Flight::register('categories_service', 'CategoriesService');
Flight::register('reviews_service', 'ReviewsServices');
Flight::register('movie_categories_service', 'MovieCategoriesServices');
Flight::register('auth_middleware', 'AuthMiddleware');

// 8. GLOBALNI MIDDLEWARE ZA AUTENTIKACIJU
Flight::route('/*', function() {
    $request = Flight::request();
    $url = $request->url;
    
    // Rute koje ne zahtijevaju autentikaciju
    $publicRoutes = [
        '/auth/login',
        '/auth/register',
        '/',
        '/docs',
        '/api-docs',
        '/test',
        '/health'
    ];
    
    // Provjeri da li je ruta javna
    $isPublic = false;
    foreach ($publicRoutes as $publicRoute) {
        if (strpos($url, $publicRoute) === 0) {
            $isPublic = true;
            break;
        }
    }
    
    // Preskoči middleware za OPTIONS request (CORS preflight)
    if ($request->method === 'OPTIONS') {
        return true;
    }
    
    // Preskoči middleware za javne rute
    if ($isPublic) {
        return true;
    }
    
    // Za sve zaštićene rute, provjeri token
    try {
        $token = Flight::request()->getHeader("Authorization");
        
        if (!$token) {
            // Provjeri alternativne header-e
            $headers = getallheaders();
            $token = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }
        
        if (!$token) {
            Flight::halt(401, json_encode([
                'success' => false,
                'message' => 'Authentication header required',
                'code' => 'AUTH_HEADER_MISSING'
            ]));
            return false;
        }
        
        // Koristi AuthMiddleware za verifikaciju tokena
        if (Flight::auth_middleware()->verifyToken($token)) {
            return true;
        }
    } catch (\Exception $e) {
        Flight::halt(401, json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'code' => 'AUTH_FAILED'
        ]));
        return false;
    }
    
    return false;
});

// 9. HELPER FUNKCIJE ZA AUTORIZACIJU
Flight::map('requireRole', function($requiredRole) {
    try {
        Flight::auth_middleware()->requireRole($requiredRole);
        return true;
    } catch (Exception $e) {
        Flight::halt(403, json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'code' => 'INSUFFICIENT_PERMISSIONS'
        ]));
        return false;
    }
});

Flight::map('requireAdmin', function() {
    Flight::requireRole(Roles::ADMIN);
});

Flight::map('requireModerator', function() {
    try {
        if (!Flight::auth_middleware()->requireModerator()) {
            Flight::halt(403, json_encode([
                'success' => false,
                'message' => 'Moderator or admin access required',
                'code' => 'MODERATOR_REQUIRED'
            ]));
        }
    } catch (Exception $e) {
        Flight::halt(403, json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'code' => 'AUTH_ERROR'
        ]));
    }
});

// 10. UČITAJ RUTE
require_once __DIR__ . '/rest/routes/AuthRoutes.php';
require_once __DIR__ . '/rest/routes/UserRoutes.php';
require_once __DIR__ . '/rest/routes/MovieRoutes.php';
require_once __DIR__ . '/rest/routes/CategoriesRoutes.php';
require_once __DIR__ . '/rest/routes/ReviewsRoutes.php';
require_once __DIR__ . '/rest/routes/MovieCategoriesRoutes.php';

// 11. DODATNE RUTE ZA MILESTONE 4

// Health check ruta
Flight::route('GET /health', function() {
    Flight::json([
        'status' => 'healthy',
        'timestamp' => date('Y-m-d H:i:s'),
        'service' => 'My Film Library API',
        'version' => '1.0.0'
    ]);
});

// Test ruta za provjeru middleware-a
Flight::route('GET /test/auth', function() {
    Flight::json([
        'success' => true,
        'message' => 'This route requires authentication',
        'user' => Flight::get('user'),
        'user_id' => Flight::get('user_id'),
        'user_role' => Flight::get('user_role')
    ]);
});

// Test ruta samo za admine
Flight::route('GET /test/admin', function() {
    // Ovo će automatski pozvati requireAdmin middleware
    Flight::requireAdmin();
    
    Flight::json([
        'success' => true,
        'message' => 'Welcome, Admin!',
        'user' => Flight::get('user')
    ]);
});

// Test ruta za user role
Flight::route('GET /test/user', function() {
    Flight::requireRole(Roles::USER);
    
    Flight::json([
        'success' => true,
        'message' => 'Welcome, User!',
        'user' => Flight::get('user')
    ]);
});

// 12. CORS HANDLING
Flight::before('start', function() {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Access-Control-Allow-Credentials: true");
    
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        Flight::json([], 200);
        exit;
    }
});

// 13. ERROR HANDLER
Flight::map('error', function(Exception $ex) {
    $errorData = [
        'success' => false,
        'message' => 'An error occurred',
        'error' => $ex->getMessage(),
        'file' => $ex->getFile(),
        'line' => $ex->getLine(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // Log error
    error_log("API Error: " . json_encode($errorData));
    
    // Return error response
    Flight::json($errorData, 500);
});

// 14. NOT FOUND HANDLER
Flight::map('notFound', function() {
    Flight::json([
        'success' => false,
        'message' => 'Endpoint not found: ' . Flight::request()->url,
        'suggestions' => [
            'public' => ['/auth/login', '/auth/register', '/health'],
            'protected' => ['/api/movies', '/api/user/profile']
        ]
    ], 404);
});

// 15. POČETNA STRANICA
Flight::route('GET /', function() {
    echo '<h1>🎬 My Film Library API</h1>';
    echo '<p><strong>Status:</strong> 🟢 Running</p>';
    echo '<p><strong>Milestone 4:</strong> ✓ Authentication & Authorization</p>';
    
    echo '<h3>Authentication Endpoints:</h3>';
    echo '<ul>';
    echo '<li><strong>POST</strong> /auth/login</li>';
    echo '<li><strong>POST</strong> /auth/register</li>';
    echo '</ul>';
    
    echo '<h3>Test Endpoints:</h3>';
    echo '<ul>';
    echo '<li><strong>GET</strong> /health - API health check</li>';
    echo '<li><strong>GET</strong> /test/auth - Requires authentication</li>';
    echo '<li><strong>GET</strong> /test/admin - Admin only</li>';
    echo '<li><strong>GET</strong> /test/user - User role required</li>';
    echo '</ul>';
    
    echo '<h3>Roles Defined:</h3>';
    echo '<pre>' . print_r(Roles::getAll(), true) . '</pre>';
    
    echo '<p><em>Use Authorization header: <code>Bearer [JWT_TOKEN]</code></em></p>';
});

// 16. START API
Flight::start();