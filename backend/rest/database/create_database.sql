@"
-- Kreiraj bazu ako ne postoji
CREATE DATABASE IF NOT EXISTS film_library;
USE film_library;

-- Users tabela (ova je najbitnija za Milestone 4)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Films tabela (za CRUD operacije)
CREATE TABLE IF NOT EXISTS films (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    director VARCHAR(100),
    year INT,
    genre VARCHAR(100),
    description TEXT,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert test podataka
INSERT INTO users (email, password, name, role) VALUES
('admin@film.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'admin'),
('user@film.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Regular User', 'user');

INSERT INTO films (title, director, year, genre, user_id) VALUES
('The Shawshank Redemption', 'Frank Darabont', 1994, 'Drama', 1),
('The Godfather', 'Francis Ford Coppola', 1972, 'Crime', 1),
('The Dark Knight', 'Christopher Nolan', 2008, 'Action', 2);
"@ | Out-File -FilePath create_database.sql