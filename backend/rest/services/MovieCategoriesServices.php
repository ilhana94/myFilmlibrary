<?php
require_once __DIR__ . '/BaseServices.php';
require_once __DIR__ . '/../dao/MovieCategoriesDao.php';

class MovieCategoriesService extends BaseService {
    public function __construct() {
        parent::__construct(new MovieCategoriesDao());
    }

    protected function validateData($data, $action) {
        parent::validateData($data, $action);

        if(!isset($data['movie_id']) || !is_numeric($data['movie_id']) || $data['movie_id'] <= 0) {
            throw new Exception("Valid movie_id is required");
        }

        if(!isset($data['category_id']) || !is_numeric($data['category_id']) || $data['category_id'] <= 0) {
            throw new Exception("Valid category_id is required");
        }

        // Provjera duplikata (opcionalno)
        // $existing = $this->dao->getByMovieAndCategory($data['movie_id'], $data['category_id']);
        // if($existing && $action === 'create') throw new Exception("This movie-category combination already exists");
    }
}
?>
