<?php
require_once __DIR__ . '/../services/ReviewsServices.php';

/**
 * @OA\Get(
 *     path="/reviews",
 *     tags={"reviews"},
 *     summary="Get all reviews",
 *     @OA\Response(response=200, description="List of reviews")
 * )
 */
Flight::route('GET /reviews', function() {
    try {
        Flight::json((new \rest\services\ReviewsServices())->getAll(), 200); 
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 *     path="/reviews/{id}",
 *     tags={"reviews"},
 *     summary="Get review by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Review found"),
 *     @OA\Response(response=404, description="Not found")
 * )
 */
Flight::route('GET /reviews/@id', function($id) {
    try {
        $review = (new \rest\services\ReviewsServices())->getById($id);  
        if ($review) {
            Flight::json($review, 200);
        } else {
            Flight::json(['error' => 'Not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/reviews",
 *     tags={"reviews"},
 *     summary="Create a new review",
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         required={"movie_id","user_id","rating"},
 *         @OA\Property(property="movie_id", type="integer", example=1),
 *         @OA\Property(property="user_id", type="integer", example=2),
 *         @OA\Property(property="rating", type="number", format="float", example=8.5),
 *         @OA\Property(property="comment", type="string", example="Great movie!")
 *     )),
 *     @OA\Response(response=201, description="Review created"),
 *     @OA\Response(response=400, description="Invalid input")
 * )
 */
Flight::route('POST /reviews', function() {
    try {
        $data = Flight::request()->data->getData();
        Flight::json((new \rest\services\ReviewsServices())->create($data), 201);  
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *     path="/reviews/{id}",
 *     tags={"reviews"},
 *     summary="Update review by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         @OA\Property(property="rating", type="number", format="float", example=9),
 *         @OA\Property(property="comment", type="string", example="Updated comment")
 *     )),
 *     @OA\Response(response=200, description="Review updated"),
 *     @OA\Response(response=400, description="Invalid input"),
 *     @OA\Response(response=404, description="Not found")
 * )
 */
Flight::route('PUT /reviews/@id', function($id) {
    try {
        $data = Flight::request()->data->getData();
        $updated = (new \rest\services\ReviewsServices())->update($id, $data);  
        if ($updated) {
            Flight::json($updated, 200);
        } else {
            Flight::json(['error' => 'Not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *     path="/reviews/{id}",
 *     tags={"reviews"},
 *     summary="Delete review by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=204, description="Review deleted"),
 *     @OA\Response(response=404, description="Not found")
 * )
 */
Flight::route('DELETE /reviews/@id', function($id) {
    try {
        $deleted = (new \rest\services\ReviewsServices())->delete($id);  
        if ($deleted) {
            Flight::json(null, 204);
        } else {
            Flight::json(['error' => 'Not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});