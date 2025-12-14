<?php
require_once __DIR__ . '/../services/MoviesServices.php';

/**
 * @OA\Get(
 *     path="/movies",
 *     tags={"movies"},
 *     summary="Get all movies - PUBLIC",
 *     @OA\Response(response=200, description="List of movies"),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */
Flight::route('GET /movies', function() {
    try {
        // Javno dostupno - svi mogu vidjeti filmove
        // Ali možete i restriktirati ako želite:
        // Flight::auth_middleware()->authorizeRole(Roles::GUEST);
        
        Flight::json((new \rest\services\MoviesServices())->getAll(), 200);  
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 *     path="/movies/{id}",
 *     tags={"movies"},
 *     summary="Get movie by ID - PUBLIC",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Movie found"),
 *     @OA\Response(response=404, description="Movie not found")
 * )
 */
Flight::route('GET /movies/@id', function($id) {
    try {
        // Javno dostupno - svi mogu vidjeti pojedinačni film
        Flight::auth_middleware()->authorizeRole(Roles::GUEST);
        
        $movie = (new \rest\services\MoviesServices())->getById($id);  
        if ($movie) {
            Flight::json($movie, 200);
        } else {
            Flight::json(['error' => 'Movie not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/movies",
 *     tags={"movies"},
 *     summary="Create a new movie - ADMIN/MODERATOR ONLY",
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         required={"title"},
 *         @OA\Property(property="title", type="string", example="Inception"),
 *         @OA\Property(property="genre", type="string", example="Sci-Fi"),
 *         @OA\Property(property="release_year", type="integer", example=2010),
 *         @OA\Property(property="director_id", type="integer", example=1)
 *     )),
 *     @OA\Response(response=201, description="Movie created"),
 *     @OA\Response(response=400, description="Invalid input"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden - Admin/Moderator required")
 * )
 */
Flight::route('POST /movies', function() {
    try {
        // SAMO ADMIN ili MODERATOR mogu kreirati filmove
        Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::MODERATOR]);
        
        // Dobavi ID korisnika koji kreira film
        $user = Flight::get('user');
        $userId = $user->id ?? null;
        
        $data = Flight::request()->data->getData();
        
        // Dodaj user_id ako nije specificiran
        if (!isset($data['user_id']) && $userId) {
            $data['user_id'] = $userId;
        }
        
        // Dodaj created_by za auditing
        $data['created_by'] = $userId;
        
        $result = (new \rest\services\MoviesServices())->create($data);  
        Flight::json([
            'success' => true,
            'message' => 'Movie created successfully',
            'data' => $result
        ], 201);  
    } catch (Exception $e) {
        $statusCode = strpos($e->getMessage(), 'Access denied') !== false ? 403 : 400;
        Flight::json([
            'success' => false,
            'error' => $e->getMessage()
        ], $statusCode);
    }
});

/**
 * @OA\Put(
 *     path="/movies/{id}",
 *     tags={"movies"},
 *     summary="Update movie by ID - ADMIN/MODERATOR ONLY",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         @OA\Property(property="title", type="string", example="Inception"),
 *         @OA\Property(property="genre", type="string", example="Sci-Fi"),
 *         @OA\Property(property="release_year", type="integer", example=2010),
 *         @OA\Property(property="director_id", type="integer", example=1)
 *     )),
 *     @OA\Response(response=200, description="Movie updated"),
 *     @OA\Response(response=400, description="Invalid input"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden - Admin/Moderator required"),
 *     @OA\Response(response=404, description="Movie not found")
 * )
 */
Flight::route('PUT /movies/@id', function($id) {
    try {
        // SAMO ADMIN ili MODERATOR mogu ažurirati filmove
        Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::MODERATOR]);
        
        // Dobavi korisnika koji ažurira
        $user = Flight::get('user');
        $userId = $user->id ?? null;
        
        $data = Flight::request()->data->getData();
        
        // Dodaj updated_by za auditing
        $data['updated_by'] = $userId;
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $updated = (new \rest\services\MoviesServices())->update($id, $data);  
        if ($updated) {
            Flight::json([
                'success' => true,
                'message' => 'Movie updated successfully',
                'data' => $updated
            ], 200);
        } else {
            Flight::json([
                'success' => false,
                'error' => 'Movie not found'
            ], 404);
        }
    } catch (Exception $e) {
        $statusCode = strpos($e->getMessage(), 'Access denied') !== false ? 403 : 400;
        Flight::json([
            'success' => false,
            'error' => $e->getMessage()
        ], $statusCode);
    }
});

/**
 * @OA\Delete(
 *     path="/movies/{id}",
 *     tags={"movies"},
 *     summary="Delete movie by ID - ADMIN ONLY",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=204, description="Movie deleted"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden - Admin required"),
 *     @OA\Response(response=404, description="Movie not found")
 * )
 */
Flight::route('DELETE /movies/@id', function($id) {
    try {
        // SAMO ADMIN može brisati filmove
        Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
        
        // Dobavi korisnika koji briše
        $user = Flight::get('user');
        $userId = $user->id ?? null;
        
        // Prvo provjeri da li film postoji i dohvati podatke za logging
        $movieService = new \rest\services\MoviesServices();
        $movie = $movieService->getById($id);
        
        if (!$movie) {
            Flight::json([
                'success' => false,
                'error' => 'Movie not found'
            ], 404);
            return;
        }
        
        $deleted = $movieService->delete($id);  
        if ($deleted) {
            // Log delete action (opcionalno)
            error_log("Movie deleted - ID: $id, Title: {$movie['title']}, Deleted by user: $userId");
            
            Flight::json([
                'success' => true,
                'message' => 'Movie deleted successfully'
            ], 200); // Možete i 204 no content, ali bolje 200 sa porukom
        } else {
            Flight::json([
                'success' => false,
                'error' => 'Failed to delete movie'
            ], 500);
        }
    } catch (Exception $e) {
        $statusCode = strpos($e->getMessage(), 'Access denied') !== false ? 403 : 500;
        Flight::json([
            'success' => false,
            'error' => $e->getMessage()
        ], $statusCode);
    }
});

/**
 * @OA\Get(
 *     path="/movies/search",
 *     tags={"movies"},
 *     summary="Search movies - PUBLIC",
 *     @OA\Parameter(name="query", in="query", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="genre", in="query", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="year", in="query", required=false, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Search results"),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */
Flight::route('GET /movies/search', function() {
    try {
        // Javno dostupno - svi mogu pretraživati
        Flight::auth_middleware()->authorizeRole(Roles::GUEST);
        
        $query = Flight::request()->query['query'] ?? null;
        $genre = Flight::request()->query['genre'] ?? null;
        $year = Flight::request()->query['year'] ?? null;
        
        $movieService = new \rest\services\MoviesServices();
        $results = $movieService->search($query, $genre, $year);
        
        Flight::json([
            'success' => true,
            'count' => count($results),
            'data' => $results
        ], 200);  
    } catch (Exception $e) {
        Flight::json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

/**
 * @OA\Post(
 *     path="/movies/{id}/favorite",
 *     tags={"movies"},
 *     summary="Add movie to favorites - AUTHENTICATED USERS ONLY",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Added to favorites"),
 *     @OA\Response(response=400, description="Already in favorites"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=404, description="Movie not found")
 * )
 */
Flight::route('POST /movies/@id/favorite', function($id) {
    try {
        // SAMO autentifikovani korisnici (USER, ADMIN, MODERATOR)
        Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN, Roles::MODERATOR]);
        
        $user = Flight::get('user');
        $userId = $user->id;
        
        $movieService = new \rest\services\MoviesServices();
        $result = $movieService->addToFavorites($userId, $id);
        
        Flight::json([
            'success' => true,
            'message' => 'Movie added to favorites',
            'data' => $result
        ], 200);
    } catch (Exception $e) {
        $statusCode = 500;
        if (strpos($e->getMessage(), 'Access denied') !== false) $statusCode = 403;
        if (strpos($e->getMessage(), 'Already in favorites') !== false) $statusCode = 400;
        if (strpos($e->getMessage(), 'Movie not found') !== false) $statusCode = 404;
        
        Flight::json([
            'success' => false,
            'error' => $e->getMessage()
        ], $statusCode);
    }
});

/**
 * @OA\Delete(
 *     path="/movies/{id}/favorite",
 *     tags={"movies"},
 *     summary="Remove movie from favorites - AUTHENTICATED USERS ONLY",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Removed from favorites"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=404, description="Not in favorites")
 * )
 */
Flight::route('DELETE /movies/@id/favorite', function($id) {
    try {
        // SAMO autentifikovani korisnici
        Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN, Roles::MODERATOR]);
        
        $user = Flight::get('user');
        $userId = $user->id;
        
        $movieService = new \rest\services\MoviesServices();
        $result = $movieService->removeFromFavorites($userId, $id);
        
        if ($result) {
            Flight::json([
                'success' => true,
                'message' => 'Movie removed from favorites'
            ], 200);
        } else {
            Flight::json([
                'success' => false,
                'error' => 'Movie was not in favorites'
            ], 404);
        }
    } catch (Exception $e) {
        $statusCode = strpos($e->getMessage(), 'Access denied') !== false ? 403 : 500;
        Flight::json([
            'success' => false,
            'error' => $e->getMessage()
        ], $statusCode);
    }
});

/**
 * @OA\Get(
 *     path="/movies/user/favorites",
 *     tags={"movies"},
 *     summary="Get user's favorite movies - AUTHENTICATED USERS ONLY",
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(response=200, description="List of favorite movies"),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */
Flight::route('GET /movies/user/favorites', function() {
    try {
        // SAMO autentifikovani korisnici
        Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN, Roles::MODERATOR]);
        
        $user = Flight::get('user');
        $userId = $user->id;
        
        $movieService = new \rest\services\MoviesServices();
        $favorites = $movieService->getUserFavorites($userId);
        
        Flight::json([
            'success' => true,
            'count' => count($favorites),
            'data' => $favorites
        ], 200);
    } catch (Exception $e) {
        $statusCode = strpos($e->getMessage(), 'Access denied') !== false ? 403 : 500;
        Flight::json([
            'success' => false,
            'error' => $e->getMessage()
        ], $statusCode);
    }
});

/**
 * @OA\Post(
 *     path="/movies/{id}/watchlist",
 *     tags={"movies"},
 *     summary="Add movie to watchlist - AUTHENTICATED USERS ONLY",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Added to watchlist"),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */
Flight::route('POST /movies/@id/watchlist', function($id) {
    try {
        // SAMO autentifikovani korisnici
        Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN, Roles::MODERATOR]);
        
        $user = Flight::get('user');
        $userId = $user->id;
        
        $movieService = new \rest\services\MoviesServices();
        $result = $movieService->addToWatchlist($userId, $id);
        
        Flight::json([
            'success' => true,
            'message' => 'Movie added to watchlist',
            'data' => $result
        ], 200);
    } catch (Exception $e) {
        $statusCode = strpos($e->getMessage(), 'Access denied') !== false ? 403 : 500;
        Flight::json([
            'success' => false,
            'error' => $e->getMessage()
        ], $statusCode);
    }
});

/**
 * @OA\Get(
 *     path="/movies/user/watchlist",
 *     tags={"movies"},
 *     summary="Get user's watchlist - AUTHENTICATED USERS ONLY",
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(response=200, description="List of watchlist movies"),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */
Flight::route('GET /movies/user/watchlist', function() {
    try {
        // SAMO autentifikovani korisnici
        Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN, Roles::MODERATOR]);
        
        $user = Flight::get('user');
        $userId = $user->id;
        
        $movieService = new \rest\services\MoviesServices();
        $watchlist = $movieService->getUserWatchlist($userId);
        
        Flight::json([
            'success' => true,
            'count' => count($watchlist),
            'data' => $watchlist
        ], 200);
    } catch (Exception $e) {
        $statusCode = strpos($e->getMessage(), 'Access denied') !== false ? 403 : 500;
        Flight::json([
            'success' => false,
            'error' => $e->getMessage()
        ], $statusCode);
    }
});