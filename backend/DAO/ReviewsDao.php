<?php
require_once __DIR__ . "/BaseDao.php";

class ReviewsDao extends BaseDao {
    public function __construct() {
        parent::__construct('reviews');
    }

    // Create
    public function createReview($movie_id, $rating, $comment) {
        return $this->insert([
            'movie_id' => $movie_id,
            'rating' => $rating,
            'comment' => $comment
        ]);
    }

    // Read all reviews for a movie
    public function getReviewsByMovie($movie_id) {
        $stmt = $this->connection->prepare("SELECT * FROM reviews WHERE movie_id = :movie_id");
        $stmt->bindParam(':movie_id', $movie_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Update
    public function updateReview($id, $data) {
        return $this->update($id, $data);
    }

    // Delete
    public function deleteReview($id) {
        return $this->delete($id);
    }
    public function getReviewsByMovieId($movie_id) {
        $stmt = $this->connection->prepare("
            SELECT * FROM reviews WHERE movie_id = :movie_id
        ");
        $stmt->bindParam(':movie_id', $movie_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAverageRating($movie_id) {
        $stmt = $this->connection->prepare("
            SELECT AVG(rating) AS average_rating
            FROM reviews
            WHERE movie_id = :movie_id
        ");
        $stmt->bindParam(':movie_id', $movie_id);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? $result['average_rating'] : null;
    }
    
}
?>
