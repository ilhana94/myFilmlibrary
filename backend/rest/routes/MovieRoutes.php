<?php
require_once __DIR__ . '/../services/MoviesServices.php';


/**
 * @OA\Get(
 *     path="/movies",
 *     tags={"movies"},
 *     summary="Get all movies",
 *     @OA\Response(response=200, description="List of movies")
 * )
 */
Flight::route('GET /movies', function() {
    try {
        Flight::json((new MoviesServices())->getAll(), 200);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 *     path="/movies/{id}",
 *     tags={"movies"},
 *     summary="Get movie by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Movie found"),
 *     @OA\Response(response=404, description="Movie not found")
 * )
 */
Flight::route('GET /movies/@id', function($id) {
    try {
        $movie = (new MoviesServices())->getById($id);
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
 *     summary="Create a new movie",
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         required={"title"},
 *         @OA\Property(property="title", type="string", example="Inception"),
 *         @OA\Property(property="genre", type="string", example="Sci-Fi"),
 *         @OA\Property(property="release_year", type="integer", example=2010),
 *         @OA\Property(property="director_id", type="integer", example=1)
 *     )),
 *     @OA\Response(response=201, description="Movie created"),
 *     @OA\Response(response=400, description="Invalid input")
 * )
 */
Flight::route('POST /movies', function() {
    try {
        $data = Flight::request()->data->getData();
        Flight::json((new MoviesServices())->create($data), 201);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *     path="/movies/{id}",
 *     tags={"movies"},
 *     summary="Update movie by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         @OA\Property(property="title", type="string", example="Inception"),
 *         @OA\Property(property="genre", type="string", example="Sci-Fi"),
 *         @OA\Property(property="release_year", type="integer", example=2010),
 *         @OA\Property(property="director_id", type="integer", example=1)
 *     )),
 *     @OA\Response(response=200, description="Movie updated"),
 *     @OA\Response(response=400, description="Invalid input"),
 *     @OA\Response(response=404, description="Movie not found")
 * )
 */
Flight::route('PUT /movies/@id', function($id) {
    try {
        $data = Flight::request()->data->getData();
        $updated = (new MoviesServices())->update($id, $data);
        if ($updated) {
            Flight::json($updated, 200);
        } else {
            Flight::json(['error' => 'Movie not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *     path="/movies/{id}",
 *     tags={"movies"},
 *     summary="Delete movie by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=204, description="Movie deleted"),
 *     @OA\Response(response=404, description="Movie not found")
 * )
 */
Flight::route('DELETE /movies/@id', function($id) {
    try {
        $deleted = (new MoviesServices())->delete($id);
        if ($deleted) {
            Flight::json(null, 204);
        } else {
            Flight::json(['error' => 'Movie not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});
