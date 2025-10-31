<?php
require_once __DIR__ . "/BaseDao.php";

class MovieCategoriesDao extends BaseDao {
    public function __construct() {
        parent::__construct('movie_categories');
    }

    // CREATE
    public function linkMovieToCategory($movie_id, $category_id) {
        return $this->insert([
            'movie_id' => $movie_id,
            'category_id' => $category_id
        ]);
    }

    // READ - sve veze
    public function getAll() {
        $stmt = $this->connection->prepare("
            SELECT mc.id, m.title AS movie, c.name AS category
            FROM movie_categories mc
            JOIN movies m ON mc.movie_id = m.id
            JOIN categories c ON mc.category_id = c.id
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // READ - po id (opcionalno korisno)
    public function getById($id) {
        $stmt = $this->connection->prepare("SELECT * FROM movie_categories WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // UPDATE
    // Možeš proslijediti oba parametra ili samo jedno polje preko $data niza
    public function updateLink($id, $movie_id = null, $category_id = null) {
        $data = [];
        if ($movie_id !== null) {
            $data['movie_id'] = $movie_id;
        }
        if ($category_id !== null) {
            $data['category_id'] = $category_id;
        }
        if (empty($data)) {
            return false; // nema polja za ažuriranje
        }
        return $this->update($id, $data);
    }

    // DELETE
    public function deleteLink($id) {
        return $this->delete($id);
    }
}
?>
