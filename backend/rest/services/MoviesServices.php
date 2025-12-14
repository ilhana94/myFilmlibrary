<?php
namespace rest\services;  

class MoviesServices extends BaseServices {  
    public function __construct() {
        parent::__construct(new \rest\dao\MoviesDao());  
    }

    protected function validateData($data, $action) {
        parent::validateData($data, $action);

        if (!isset($data['title']) || trim($data['title']) === '') {
            throw new \Exception("Movie title is required");  
        }

        if (isset($data['release_year'])) {
            $year = (int)$data['release_year'];
            $currentYear = (int)date('Y');
            if ($year < 1900 || $year > $currentYear) {
                throw new \Exception("Release year must be between 1900 and $currentYear");  
            }
        }

        if (isset($data['rating'])) {
            $rating = (float)$data['rating'];
            if ($rating < 1 || $rating > 10) {
                throw new \Exception("Rating must be between 1 and 10");  
            }
        }
    }

    /**
     * Get all movies with optional filters
     */
    public function getAll($filters = []) {
        try {
            return $this->dao->getAll($filters);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching movies: " . $e->getMessage());
        }
    }

    /**
     * Get movie by ID
     */
    public function getById($id) {
        try {
            $movie = $this->dao->getById($id);
            if (!$movie) {
                throw new \Exception("Movie with ID $id not found");
            }
            return $movie;
        } catch (\Exception $e) {
            throw new \Exception("Error fetching movie: " . $e->getMessage());
        }
    }

    /**
     * Search movies with filters
     */
    public function search($query = null, $genre = null, $year = null, $limit = null) {
        try {
            $filters = [];
            
            if ($query) {
                $filters['search'] = $query;
            }
            
            if ($genre) {
                $filters['genre'] = $genre;
            }
            
            if ($year) {
                $filters['year'] = $year;
            }
            
            if ($limit) {
                $filters['limit'] = $limit;
            }
            
            return $this->dao->getAll($filters);
        } catch (\Exception $e) {
            throw new \Exception("Error searching movies: " . $e->getMessage());
        }
    }

    /**
     * Create a new movie
     */
    public function create($data) {
        try {
            // Validacija podataka
            $this->validateData($data, 'create');
            
            // Dodaj audit polja ako postoje
            if (Flight::get('user_id')) {
                $data['created_by'] = Flight::get('user_id');
                $data['updated_by'] = Flight::get('user_id');
            }
            
            return $this->dao->create($data);
        } catch (\Exception $e) {
            throw new \Exception("Error creating movie: " . $e->getMessage());
        }
    }

    /**
     * Update movie
     */
    public function update($id, $data) {
        try {
            // Provjeri da li film postoji
            $existing = $this->dao->getById($id);
            if (!$existing) {
                throw new \Exception("Movie with ID $id not found");
            }
            
            // Validacija podataka
            $this->validateData($data, 'update');
            
            // Dodaj audit polja
            if (Flight::get('user_id')) {
                $data['updated_by'] = Flight::get('user_id');
                $data['updated_at'] = date('Y-m-d H:i:s');
            }
            
            return $this->dao->update($id, $data);
        } catch (\Exception $e) {
            throw new \Exception("Error updating movie: " . $e->getMessage());
        }
    }

    /**
     * Delete movie
     */
    public function delete($id) {
        try {
            // Provjeri da li film postoji
            $existing = $this->dao->getById($id);
            if (!$existing) {
                throw new \Exception("Movie with ID $id not found");
            }
            
            // Dobavi informacije za logging (opcionalno)
            $movieTitle = $existing['title'] ?? 'Unknown';
            $userId = Flight::get('user_id');
            
            // Obriši film
            $result = $this->dao->delete($id);
            
            if ($result) {
                // Log delete action
                error_log("Movie deleted - ID: $id, Title: $movieTitle, Deleted by user: $userId");
            }
            
            return $result;
        } catch (\Exception $e) {
            throw new \Exception("Error deleting movie: " . $e->getMessage());
        }
    }

    /**
     * Add movie to user's favorites
     */
    public function addToFavorites($userId, $movieId) {
        try {
            // Provjeri da li film postoji
            $movie = $this->dao->getById($movieId);
            if (!$movie) {
                throw new \Exception("Movie not found");
            }
            
            // Provjeri da li je već u favoritima
            $checkStmt = Flight::db()->prepare(
                "SELECT * FROM user_favorites WHERE user_id = ? AND movie_id = ?"
            );
            $checkStmt->execute([$userId, $movieId]);
            
            if ($checkStmt->fetch()) {
                throw new \Exception("Movie already in favorites");
            }
            
            // Dodaj u favorite
            $stmt = Flight::db()->prepare(
                "INSERT INTO user_favorites (user_id, movie_id, created_at) 
                 VALUES (?, ?, NOW())"
            );
            
            $result = $stmt->execute([$userId, $movieId]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Movie added to favorites',
                    'movie_id' => $movieId,
                    'user_id' => $userId
                ];
            }
            
            throw new \Exception("Failed to add movie to favorites");
            
        } catch (\Exception $e) {
            throw new \Exception("Error adding to favorites: " . $e->getMessage());
        }
    }

    /**
     * Remove movie from favorites
     */
    public function removeFromFavorites($userId, $movieId) {
        try {
            $stmt = Flight::db()->prepare(
                "DELETE FROM user_favorites WHERE user_id = ? AND movie_id = ?"
            );
            
            $result = $stmt->execute([$userId, $movieId]);
            
            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Movie removed from favorites',
                    'movie_id' => $movieId,
                    'user_id' => $userId
                ];
            } else {
                throw new \Exception("Movie was not in favorites");
            }
            
        } catch (\Exception $e) {
            throw new \Exception("Error removing from favorites: " . $e->getMessage());
        }
    }

    /**
     * Get user's favorite movies
     */
    public function getUserFavorites($userId) {
        try {
            $stmt = Flight::db()->prepare(
                "SELECT m.*, uf.created_at as favorited_at 
                 FROM movies m
                 JOIN user_favorites uf ON m.id = uf.movie_id
                 WHERE uf.user_id = ?
                 ORDER BY uf.created_at DESC"
            );
            $stmt->execute([$userId]);
            
            $movies = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'count' => count($movies),
                'movies' => $movies
            ];
            
        } catch (\Exception $e) {
            throw new \Exception("Error fetching favorites: " . $e->getMessage());
        }
    }

    /**
     * Add movie to watchlist
     */
    public function addToWatchlist($userId, $movieId) {
        try {
            // Provjeri da li film postoji
            $movie = $this->dao->getById($movieId);
            if (!$movie) {
                throw new \Exception("Movie not found");
            }
            
            // Provjeri da li je već u watchlist
            $checkStmt = Flight::db()->prepare(
                "SELECT * FROM user_watchlist WHERE user_id = ? AND movie_id = ?"
            );
            $checkStmt->execute([$userId, $movieId]);
            
            if ($checkStmt->fetch()) {
                throw new \Exception("Movie already in watchlist");
            }
            
            // Dodaj u watchlist
            $stmt = Flight::db()->prepare(
                "INSERT INTO user_watchlist (user_id, movie_id, added_at) 
                 VALUES (?, ?, NOW())"
            );
            
            $result = $stmt->execute([$userId, $movieId]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Movie added to watchlist',
                    'movie_id' => $movieId,
                    'user_id' => $userId
                ];
            }
            
            throw new \Exception("Failed to add movie to watchlist");
            
        } catch (\Exception $e) {
            throw new \Exception("Error adding to watchlist: " . $e->getMessage());
        }
    }

    /**
     * Remove movie from watchlist
     */
    public function removeFromWatchlist($userId, $movieId) {
        try {
            $stmt = Flight::db()->prepare(
                "DELETE FROM user_watchlist WHERE user_id = ? AND movie_id = ?"
            );
            
            $result = $stmt->execute([$userId, $movieId]);
            
            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Movie removed from watchlist',
                    'movie_id' => $movieId,
                    'user_id' => $userId
                ];
            } else {
                throw new \Exception("Movie was not in watchlist");
            }
            
        } catch (\Exception $e) {
            throw new \Exception("Error removing from watchlist: " . $e->getMessage());
        }
    }

    /**
     * Get user's watchlist
     */
    public function getUserWatchlist($userId) {
        try {
            $stmt = Flight::db()->prepare(
                "SELECT m.*, uw.added_at as watchlisted_at 
                 FROM movies m
                 JOIN user_watchlist uw ON m.id = uw.movie_id
                 WHERE uw.user_id = ?
                 ORDER BY uw.added_at DESC"
            );
            $stmt->execute([$userId]);
            
            $movies = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'count' => count($movies),
                'movies' => $movies
            ];
            
        } catch (\Exception $e) {
            throw new \Exception("Error fetching watchlist: " . $e->getMessage());
        }
    }

    /**
     * Get movies by category
     */
    public function getByCategory($categoryId) {
        try {
            $stmt = Flight::db()->prepare(
                "SELECT m.* FROM movies m
                 JOIN movie_categories mc ON m.id = mc.movie_id
                 WHERE mc.category_id = ?"
            );
            $stmt->execute([$categoryId]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching movies by category: " . $e->getMessage());
        }
    }

    /**
     * Get recent movies
     */
    public function getRecent($limit = 10) {
        try {
            $stmt = Flight::db()->prepare(
                "SELECT * FROM movies 
                 ORDER BY created_at DESC 
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching recent movies: " . $e->getMessage());
        }
    }

    /**
     * Get top rated movies
     */
    public function getTopRated($limit = 10) {
        try {
            $stmt = Flight::db()->prepare(
                "SELECT * FROM movies 
                 WHERE rating IS NOT NULL 
                 ORDER BY rating DESC 
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching top rated movies: " . $e->getMessage());
        }
    }

    /**
     * Check if user has permission to modify movie
     * (Za Milestone 4 - autorizacija na servis nivou)
     */
    public function checkMoviePermission($movieId, $userId, $userRole) {
        try {
            $movie = $this->dao->getById($movieId);
            
            if (!$movie) {
                return false;
            }
            
            // Admin može sve
            if ($userRole === \data\Roles::ADMIN) {
                return true;
            }
            
            // Moderator može sve osim brisanja
            if ($userRole === \data\Roles::MODERATOR) {
                return true;
            }
            
            // Provjeri da li je korisnik kreator filma
            if (isset($movie['created_by']) && $movie['created_by'] == $userId) {
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            throw new \Exception("Error checking permission: " . $e->getMessage());
        }
    }

    /**
     * Get movie statistics for dashboard
     */
    public function getStatistics() {
        try {
            $stats = [];
            
            // Total movies
            $stmt = Flight::db()->prepare("SELECT COUNT(*) as total FROM movies");
            $stmt->execute();
            $stats['total_movies'] = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
            
            // Movies by genre
            $stmt = Flight::db()->prepare(
                "SELECT genre, COUNT(*) as count 
                 FROM movies 
                 WHERE genre IS NOT NULL 
                 GROUP BY genre 
                 ORDER BY count DESC"
            );
            $stmt->execute();
            $stats['by_genre'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Average rating
            $stmt = Flight::db()->prepare(
                "SELECT AVG(rating) as avg_rating FROM movies WHERE rating IS NOT NULL"
            );
            $stmt->execute();
            $stats['avg_rating'] = $stmt->fetch(\PDO::FETCH_ASSOC)['avg_rating'];
            
            // Recent additions
            $stats['recent_movies'] = $this->getRecent(5);
            
            return [
                'success' => true,
                'statistics' => $stats
            ];
        } catch (\Exception $e) {
            throw new \Exception("Error fetching statistics: " . $e->getMessage());
        }
    }
}
?>