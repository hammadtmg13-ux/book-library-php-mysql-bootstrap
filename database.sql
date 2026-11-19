-- BookNest - Personal Library Tracker
-- Database setup: run with: mysql -u root -p < database.sql

CREATE DATABASE IF NOT EXISTS book_library CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE book_library;

CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    genre ENUM('fiction', 'non-fiction', 'science', 'history', 'biography', 'fantasy', 'self-help', 'mystery') NOT NULL DEFAULT 'fiction',
    status ENUM('to-read', 'reading', 'finished') NOT NULL DEFAULT 'to-read',
    rating TINYINT NULL DEFAULT NULL,
    pages INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample books to get started
INSERT INTO books (title, author, genre, status, rating, pages) VALUES
('Clean Code', 'Robert C. Martin', 'non-fiction', 'finished', 5, 464),
('The Alchemist', 'Paulo Coelho', 'fiction', 'finished', 4, 208),
('A Brief History of Time', 'Stephen Hawking', 'science', 'reading', NULL, 212),
('Atomic Habits', 'James Clear', 'self-help', 'to-read', NULL, 320),
('The Hobbit', 'J. R. R. Tolkien', 'fantasy', 'finished', 5, 310);
