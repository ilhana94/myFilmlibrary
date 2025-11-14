<?php
require_once __DIR__ . '/BaseServices.php';
require_once __DIR__ . '/../dao/ReviewsDao.php';

class ReviewsService extends BaseService {
    public function __construct() {
        parent::__construct(new ReviewsDao());
    }

    protected function validateData($data, $action) {
        parent::validateData($data, $action);

        if(!isset($data['movie_id']) || !is_numeric($data['movie_id']) || $data['movie_id'] <= 0) {
            throw new Exception("Valid movie_id is required");
        }

        if(!isset($data['user_id']) || !is_numeric($data['user_id']) || $data['user_id'] <= 0) {
            throw new Exception("Valid user_id is required");
        }

        if(!isset($data['rating']) || !is_numeric($data['rating']) || $data['rating'] < 1 || $data['rating'] > 10) {
            throw new Exception("Rating must be between 1 and 10");
        }

        if(isset($data['comment']) && strlen($data['comment']) > 500) {
            throw new Exception("Comment cannot be longer than 500 characters");
        }
    }
}
?>
