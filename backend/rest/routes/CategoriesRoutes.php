<?php
require_once __DIR__ . '/../services/CategoriesService.php';

// TEST RUTA - probaj ovu prvo
Flight::route('GET /categories/test', function() {
    echo "TEST RUTA RADI!";
    exit;
});

/**
 * @OA\Get(
 *     path="/categories",
 *     tags={"categories"},
 *     summary="Get all categories",
 *     @OA\Response(
 *         response=200,
 *         description="List of categories"
 *     )
 * )
 */
Flight::route('GET /categories', function() {
    Flight::json((new \rest\services\CategoriesService())->getAll());  // 👈 DODAJ NAMESPACE
});

/**
 * @OA\Get(
 *     path="/categories/{id}",
 *     tags={"categories"},
 *     summary="Get category by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(response=200, description="Category found")
 * )
 */
Flight::route('GET /categories/@id', function($id) {
    Flight::json((new \rest\services\CategoriesService())->getById($id));  // 👈 DODAJ NAMESPACE
});

/**
 * @OA\Post(
 *     path="/categories",
 *     tags={"categories"},
 *     summary="Create a new category",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Action")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Category created")
 * )
 */
Flight::route('POST /categories', function() {
    $data = Flight::request()->data->getData();
    Flight::json((new \rest\services\CategoriesService())->create($data));  // 👈 DODAJ NAMESPACE
});

/**
 * @OA\Put(
 *     path="/categories/{id}",
 *     tags={"categories"},
 *     summary="Update category by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(required=true,
 *         @OA\JsonContent(@OA\Property(property="name", type="string", example="Comedy"))
 *     ),
 *     @OA\Response(response=200, description="Category updated")
 * )
 */
Flight::route('PUT /categories/@id', function($id) {
    $data = Flight::request()->data->getData();
    Flight::json((new \rest\services\CategoriesService())->update($id, $data));  // 👈 DODAJ NAMESPACE
});

/**
 * @OA\Delete(
 *     path="/categories/{id}", 
 *     tags={"categories"},
 *     summary="Delete category by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(response=200, description="Category deleted")
 * )
 */
Flight::route('DELETE /categories/@id', function($id) {
    Flight::json((new \rest\services\CategoriesService())->delete($id));  // 👈 DODAJ NAMESPACE
});