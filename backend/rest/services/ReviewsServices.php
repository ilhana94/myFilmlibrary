<?php
namespace rest\services;

class ReviewsServices extends BaseServices {
    public function __construct() {
        parent::__construct(new \rest\dao\ReviewsDao());
    }

    // 👇 DODAJTE OVU METODU
    public function getAll() {
        return $this->dao->getAll();
    }

    // 👇 DODAJTE I OVE METODE KOJE SE POZIVAJU IZ ROUTES
    public function getById($id) {
        return $this->dao->getReviewById($id);
    }

    public function create($data) {
        $this->validateData($data, 'create');
        return $this->dao->createReview(
            $data['movie_id'], 
            $data['user_id'],  // 👈 DODAJ user_id
            $data['rating'], 
            $data['comment'] ?? ''  // 👈 DODAJ default vrijednost za comment
        );
    }

    public function update($id, $data) {
        $this->validateData($data, 'update');
        return $this->dao->updateReview($id, $data);
    }

    public function delete($id) {
        return $this->dao->deleteReview($id);
    }

    protected function validateData($data, $action) {
        parent::validateData($data, $action);

        if(!isset($data['movie_id']) || !is_numeric($data['movie_id']) || $data['movie_id'] <= 0) {
            throw new \Exception("Valid movie_id is required");
        }

        if(!isset($data['user_id']) || !is_numeric($data['user_id']) || $data['user_id'] <= 0) {
            throw new \Exception("Valid user_id is required");
        }

        if(!isset($data['rating']) || !is_numeric($data['rating']) || $data['rating'] < 1 || $data['rating'] > 10) {
            throw new \Exception("Rating must be between 1 and 10");
        }

        if(isset($data['comment']) && strlen($data['comment']) > 500) {
            throw new \Exception("Comment cannot be longer than 500 characters");
        }
    }
}
?>