<?php
require_once __DIR__ . '/../services/UserServices.php';

/**
 * @OA\Get(
 *     path="/users",
 *     tags={"users"},
 *     summary="Get all users",
 *     @OA\Response(response=200, description="List of users")
 * )
 */
Flight::route('GET /users', function() {
    try {
        Flight::json((new \rest\services\UserServices())->getAll(), 200);  
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Get user by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="User found"),
 *     @OA\Response(response=404, description="Not found")
 * )
 */
Flight::route('GET /users/@id', function($id) {
    try {
        $user = (new \rest\services\UserServices())->getById($id);  
        if ($user) {
            Flight::json($user, 200);
        } else {
            Flight::json(['error' => 'Not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/users",
 *     tags={"users"},
 *     summary="Create a new user",
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         required={"username","email","password"},
 *         @OA\Property(property="username", type="string", example="john_doe"),
 *         @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *         @OA\Property(property="password", type="string", example="secret123"),
 *         @OA\Property(property="role", type="string", example="user")
 *     )),
 *     @OA\Response(response=201, description="User created"),
 *     @OA\Response(response=400, description="Invalid input")
 * )
 */
Flight::route('POST /users', function() {
    try {
        $data = Flight::request()->data->getData();
        Flight::json((new \rest\services\UserServices())->create($data), 201); 
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Update user by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         @OA\Property(property="username", type="string", example="john_doe_updated"),
 *         @OA\Property(property="email", type="string", format="email", example="john_new@example.com"),
 *         @OA\Property(property="password", type="string", example="newsecret123"),
 *         @OA\Property(property="role", type="string", example="admin")
 *     )),
 *     @OA\Response(response=200, description="User updated"),
 *     @OA\Response(response=400, description="Invalid input"),
 *     @OA\Response(response=404, description="Not found")
 * )
 */
Flight::route('PUT /users/@id', function($id) {
    try {
        $data = Flight::request()->data->getData();
        $updated = (new \rest\services\UserServices())->update($id, $data); 
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
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Delete user by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=204, description="User deleted"),
 *     @OA\Response(response=404, description="Not found")
 * )
 */
Flight::route('DELETE /users/@id', function($id) {
    try {
        $deleted = (new \rest\services\UserServices())->delete($id);  
        if ($deleted) {
            Flight::json(null, 204);
        } else {
            Flight::json(['error' => 'Not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});