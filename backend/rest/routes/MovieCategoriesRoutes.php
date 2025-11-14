<?php
require_once __DIR__ . '/../services/MovieCategoriesServices.php';

Flight::route('/movie_categories', function() {
    $service = new MovieCategoriesServices();
    Flight::json($service->getAll());
});

Flight::route('GET /movie_categories', function() {
    try {
        Flight::json((new MovieCategoriesServices())->getAll(), 200);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

Flight::route('GET /movie_categories/@id', function($id) {
    try {
        $link = (new MovieCategoriesServices())->getById($id);
        if ($link) {
            Flight::json($link, 200);
        } else {
            Flight::json(['error' => 'Not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

Flight::route('POST /movie_categories', function() {
    try {
        $data = Flight::request()->data->getData();
        Flight::json((new MovieCategoriesServices())->create($data), 201);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

Flight::route('PUT /movie_categories/@id', function($id) {
    try {
        $data = Flight::request()->data->getData();
        $updated = (new MovieCategoriesServices())->update($id, $data);
        if ($updated) {
            Flight::json($updated, 200);
        } else {
            Flight::json(['error' => 'Not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

Flight::route('DELETE /movie_categories/@id', function($id) {
    try {
        $deleted = (new MovieCategoriesServices())->delete($id);
        if ($deleted) {
            Flight::json(null, 204);
        } else {
            Flight::json(['error' => 'Not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});
