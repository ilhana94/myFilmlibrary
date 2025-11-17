<?php
namespace rest\services;  // 👈 DODAJ OVAJ NAMESPACE

class MoviesServices extends BaseServices {  // 👈 Promijeni u "BaseServices" (s na kraju) i "MovieServices" (bez s na kraju)
    public function __construct() {
        parent::__construct(new \rest\dao\MoviesDao());  // 👈 Dodaj namespace
    }

    protected function validateData($data, $action) {
        parent::validateData($data, $action);

        if (!isset($data['title']) || trim($data['title']) === '') {
            throw new \Exception("Movie title is required");  // 👈 Dodaj backslash
        }

        if (isset($data['release_year'])) {
            $year = (int)$data['release_year'];
            $currentYear = (int)date('Y');
            if ($year < 1900 || $year > $currentYear) {
                throw new \Exception("Release year must be between 1900 and $currentYear");  // 👈 Dodaj backslash
            }
        }

        if (isset($data['rating'])) {
            $rating = (float)$data['rating'];
            if ($rating < 1 || $rating > 10) {
                throw new \Exception("Rating must be between 1 and 10");  // 👈 Dodaj backslash
            }
        }
    }
}
?>