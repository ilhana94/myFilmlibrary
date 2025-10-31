<?php
require_once __DIR__ . "/BaseDao.php";

class MoviesDao extends BaseDao {
    public function __construct() {
        parent::__construct('movies');
    }

    // CREATE
    public function createMovie($title, $genre, $release_year, $director_id) {
        return $this->insert([
            'title' => $title,
            'genre' => $genre,
            'release_year' => $release_year,
            'director_id' => $director_id
        ]);
    }

    // READ (single movie by title)
    public function getByTitle($title) {
        $stmt = $this->connection->prepare("SELECT * FROM movies WHERE title = :title");
        $stmt->bindParam(':title', $title);
        $stmt->execute();
        return $stmt->fetch();
    }

    // READ (all movies)
    public function getAll() {
        $stmt = $this->connection->prepare("SELECT * FROM movies");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // UPDATE
    public function updateMovie($id, $data) {
        return $this->update($id, $data);
    }

    // DELETE
    public function deleteMovie($id) {
        return $this->delete($id);
    }
    public function getMoviesWithCategories() {
        $query = "
            SELECT m.*, c.name AS category_name
            FROM movies m
            LEFT JOIN movie_categories mc ON m.id = mc.movie_id
            LEFT JOIN categories c ON mc.category_id = c.id
        ";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
}
?>
