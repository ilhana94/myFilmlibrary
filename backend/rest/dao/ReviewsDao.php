<?php
namespace rest\dao;

class ReviewsDao extends BaseDao {
    public function __construct() {
        parent::__construct('reviews');
    }

    // CREATE
    public function createReview($movie_id, $user_id, $rating, $comment) {  
        return $this->insert([
            'movie_id' => $movie_id,
            'user_id' => $user_id,  
            'rating' => $rating,
            'comment' => $comment
        ]);
    }

    // READ - specifične metode za reviews
    public function getReviewsByMovie($movie_id) {
        $stmt = $this->connection->prepare("
            SELECT * FROM reviews WHERE movie_id = :movie_id ORDER BY id DESC
        ");
        $stmt->bindParam(':movie_id', $movie_id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Jedna recenzija po ID-u
    public function getReviewById($id) {
        $stmt = $this->connection->prepare("
            SELECT * FROM reviews WHERE id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Prosječna ocjena za film
    public function getAverageRating($movie_id) {
        $stmt = $this->connection->prepare("
            SELECT AVG(rating) AS average_rating
            FROM reviews
            WHERE movie_id = :movie_id
        ");
        $stmt->bindParam(':movie_id', $movie_id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? $result['average_rating'] : null;
    }

    // UPDATE
    public function updateReview($id, $data) {
        return $this->update($id, $data);
    }

    // DELETE
    public function deleteReview($id) {
        return $this->delete($id);
    }
}
?>