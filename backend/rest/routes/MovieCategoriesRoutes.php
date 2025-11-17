<?php
require_once __DIR__ . '/../services/MovieCategoriesServices.php';

Flight::route('GET /movie-categories', function() {
    try {
        Flight::json((new \rest\services\MovieCategoriesServices())->getAll(), 200);  // 👈 DODAJ NAMESPACE
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

Flight::route('GET /movie-categories/@id', function($id) {
    try {
        $link = (new \rest\services\MovieCategoriesServices())->getById($id);  // 👈 DODAJ NAMESPACE
        if ($link) {
            Flight::json($link, 200);
        } else {
            Flight::json(['error' => 'Not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

Flight::route('POST /movie-categories', function() {
    try {
        $data = Flight::request()->data->getData();
        Flight::json((new \rest\services\MovieCategoriesServices())->create($data), 201);  // 👈 DODAJ NAMESPACE
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

Flight::route('PUT /movie-categories/@id', function($id) {
    try {
        $data = Flight::request()->data->getData();
        $updated = (new \rest\services\MovieCategoriesServices())->update($id, $data);  // 👈 DODAJ NAMESPACE
        if ($updated) {
            Flight::json($updated, 200);
        } else {
            Flight::json(['error' => 'Not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

Flight::route('DELETE /movie-categories/@id', function($id) {
    try {
        $deleted = (new \rest\services\MovieCategoriesServices())->delete($id);  // 👈 DODAJ NAMESPACE
        if ($deleted) {
            Flight::json(null, 204);
        } else {
            Flight::json(['error' => 'Not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});